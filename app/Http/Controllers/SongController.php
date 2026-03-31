<?php

namespace App\Http\Controllers;

use App\CustomSorts\SongStatusSort;
use App\Http\Requests\SongRequest;
use App\Models\Ensemble;
use App\Models\Membership;
use App\Models\Song;
use App\Models\SongCategory;
use App\Models\SongStatus;
use App\Models\VoicePart;
use App\Notifications\SongUpdated;
use App\Notifications\SongUploaded;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class SongController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Song::class);
    }

    public function index(Request $request): InertiaResponse
    {
        $includePending = auth()->user()?->isSuperAdmin || auth()->user()?->membership->hasAbility('songs_update');
        $statuses = SongStatus::query()
            ->when(! $includePending, fn ($query) => $query->where('title', '!=', 'Pending'))
            ->get();
        $defaultStatuses = $statuses->where('title', '!=', 'Archived')->pluck('id')->toArray();

        $includeNonAuditionSongs = auth()->user()?->isSuperAdmin || auth()->user()?->membership->status->name === 'Members';
        $showForProspectsDefault = $includeNonAuditionSongs ? [false, true] : [true];

        $totalEnsemblesCount = Ensemble::count();
        $userEnsemblesCount = (auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasAbility('songs_update'))
            ? $totalEnsemblesCount
            : auth()->user()?->membership?->enrolments->count() ?? 0;

        $ensembles = Ensemble::query()
            ->when(! (auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasAbility('songs_update')), function (Builder $query) {
                $query->whereIn('id', auth()->user()?->membership?->enrolments->pluck('ensemble_id') ?? []);
            })
            ->get();

        return Inertia::render('Songs/Index', [
            'songs' => $this->getSongs($includePending, $defaultStatuses, $includeNonAuditionSongs, $showForProspectsDefault),
            'statuses' => SongStatus::query()
                ->when(! $includePending, fn ($query) => $query->where('title', '!=', 'Pending'))
                ->get()
                ->values(),
            'defaultStatuses' => $defaultStatuses,
            'categories' => SongCategory::all()->values(),
            'showForProspectsDefault' => $showForProspectsDefault,
            'userEnsemblesCount' => $userEnsemblesCount,
            'ensembles' => $ensembles,
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Songs/Create', [
            'categories' => SongCategory::all()->values(),
            'statuses' => SongStatus::all()->values(),
            'pitches' => Song::PITCHES,
            'ensembles' => Ensemble::all()->values(),
        ]);
    }

    public function store(SongRequest $request): RedirectResponse
    {
        $song = Song::create($request->safe()->except('send_notification'));

        if ($request->input('send_notification')) {
            $notification = new SongUploaded($song);
            $recipients = Membership::active()->with('user')->get()->pluck('user');
            Notification::send($recipients, $notification);
            $notification->log($song->id, $recipients->first());
        }

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Song created. ']);
    }

    public function show(Song $song): InertiaResponse
    {
        $assessment_ready_count = $song->members()->active()->wherePivot('status', 'assessment-ready')->count();
        $performance_ready_count = $song->members()->active()->wherePivot('status', 'performance-ready')->count();

        $voice_parts_performance_ready_count = VoicePart::withCount([
            'enrolments as singers_count' => function (Builder $query) use ($song) {
                $query->whereHas('membership', function (Builder $query) {
                    $query->active();
                });
            },
            'enrolments as performance_ready_count' => function (Builder $query) use ($song) {
                $query
                    ->whereHas('membership', function (Builder $query) use ($song) {
                        $query
                            ->active()
                            ->whereHas('songs', function (Builder $query) use ($song) {
                                $query->where('songs.id', $song->id)
                                    ->where('membership_song.status', 'performance-ready');
                            });
                    })
                    ->with('songs');
            },
        ])->get();

        $song->can = [
            'update_song' => auth()->user()?->can('update', $song),
            'delete_song' => auth()->user()?->can('delete', $song),
        ];

        $song->append('my_learning');

        return Inertia::render('Songs/Show', [
            'song' => $song,
            'attachment_types' => $song->attachments->mapToGroups(function ($attachment) {
                return [$attachment->type => $attachment];
            })->sortBy(function ($attachments, $type) {
                return match ($type) {
                    'sheet-music' => 0,
                    'full-mix-demo' => 1,
                    'learning-tracks' => 2,
                    'youtube' => 3,
                    'other' => 4,
                };
            }),
            'status_count' => [
                'performance_ready' => $performance_ready_count,
                'assessment_ready' => $assessment_ready_count,
                'learning' => Membership::active()->count() - $assessment_ready_count - $performance_ready_count,
            ],
            'voice_parts_count' => [
                'performance_ready' => $voice_parts_performance_ready_count,
            ],
        ]);
    }

    public function edit(Song $song): InertiaResponse
    {
        return Inertia::render('Songs/Edit', [
            'categories' => SongCategory::all()->values(),
            'statuses' => SongStatus::all()->values(),
            'pitches' => Song::PITCHES,
            'song' => $song,
            'ensembles' => Ensemble::all()->values(),
        ]);
    }

    public function update(SongRequest $request, Song $song): RedirectResponse
    {
        $song->update($request->safe()->except('send_notification'));

        if ($request->input('send_notification')) {
            $notification = new SongUpdated($song);
            $recipients = Membership::active()->with('user')->get()->pluck('user');
            Notification::send($recipients, $notification);
            $notification->log($song->id, $recipients->first());
        }

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Song updated. ']);
    }

    public function destroy(Song $song): RedirectResponse
    {
        $song->delete();

        return redirect()
            ->route('songs.index')
            ->with(['status' => 'Song deleted. ']);
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $this->authorize('update', Song::class);

        $request->validate([
            'song_ids' => 'required|array',
            'song_ids.*' => 'exists:songs,id',
            'status_id' => 'nullable|exists:song_statuses,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:song_categories,id',
            'ensemble_ids' => 'nullable|array',
            'ensemble_ids.*' => 'exists:ensembles,id',
        ]);

        $songIds = $request->input('song_ids');

        if ($request->filled('status_id')) {
            Song::whereIn('id', $songIds)->update(['status_id' => $request->status_id]);
        }

        if ($request->has('category_ids')) {
            $categoryIds = $request->input('category_ids');
            foreach ($songIds as $songId) {
                // We can use the song ID directly without fetching the model.
                (new Song(['id' => $songId]))->setRawAttributes(['id' => $songId], true)->categories()->sync($categoryIds);
            }
        }

        if ($request->has('ensemble_ids')) {
            $ensembleIds = $request->input('ensemble_ids');
            foreach ($songIds as $songId) {
                (new Song(['id' => $songId]))->setRawAttributes(['id' => $songId], true)->ensembles()->sync($ensembleIds);
            }
        }

        return redirect()
            ->route('songs.index')
            ->with(['status' => count($songIds) . ' songs updated. ']);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $this->authorize('delete', Song::class);

        $request->validate([
            'song_ids' => 'required|array',
            'song_ids.*' => 'exists:songs,id',
        ]);

        $songIds = $request->input('song_ids');

        Song::whereIn('id', $songIds)->delete();

        return redirect()
            ->route('songs.index')
            ->with(['status' => count($songIds) . ' songs deleted. ']);
    }

    private function getSongs(bool $includePending, array $defaultStatuses, bool $includeNonAuditionSongs, array $showForProspectsDefault): LengthAwarePaginator
    {
        $userEnsembles = auth()->user()?->membership?->enrolments->pluck('ensemble_id');
        $canUpdate = auth()->user()?->membership?->hasAbility('songs_update');

        return QueryBuilder::for(Song::class)
            ->when(! $canUpdate && ! auth()->user()?->isSuperAdmin, function (Builder $query) use ($userEnsembles) {
                $query->where(function (Builder $query) use ($userEnsembles) {
                    $query->whereDoesntHave('ensembles')
                        ->orWhereHas('ensembles', function (Builder $query) use ($userEnsembles) {
                            $query->whereIn('ensembles.id', $userEnsembles ?? []);
                        });
                });
            })
            ->allowedFilters([
                'title',
                AllowedFilter::exact('status.id')
                    ->ignore($includePending ? [] : [SongStatus::where('title', '=', 'Pending')->value('id')])
                    ->default($defaultStatuses),
                AllowedFilter::callback('show_for_prospects', fn(Builder $query, $value) => $query->whereIn('show_for_prospects', $includeNonAuditionSongs ? $value : [true])
                )
                    ->default($showForProspectsDefault),
                AllowedFilter::exact('categories.id'),
                AllowedFilter::exact('ensembles.id'),
            ])
            ->defaultSort('title')
            ->allowedSorts([
                'title',
                'created_at',
                AllowedSort::custom('status-title', new SongStatusSort(), 'title'),
            ])
            ->paginate(50)->appends(request()->query());
    }
}
