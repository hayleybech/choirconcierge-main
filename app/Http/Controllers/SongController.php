<?php

namespace App\Http\Controllers;

use App\CustomSorts\SongStatusSort;
use App\Http\Requests\SongRequest;
use App\Models\Membership;
use App\Models\Song;
use App\Models\SongCategory;
use App\Models\SongStatus;
use App\Models\VoicePart;
use App\Notifications\SongUpdated;
use App\Notifications\SongUploaded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

        $includeNonAuditionSongs = auth()->user()?->isSuperAdmin || auth()->user()?->membership->category->name === 'Members';
        $showForProspectsDefault = $includeNonAuditionSongs ? [false, true] : [true];

        return Inertia::render('Songs/Index', [
            'songs' => $this->getSongs($includePending, $defaultStatuses, $includeNonAuditionSongs, $showForProspectsDefault)->values(),
            'statuses' => SongStatus::query()
                ->when(! $includePending, fn ($query) => $query->where('title', '!=', 'Pending'))
                ->get()
                ->values(),
            'defaultStatuses' => $defaultStatuses,
            'categories' => SongCategory::all()->values(),
            'showForProspectsDefault' => $showForProspectsDefault,
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Songs/Create', [
            'categories' => SongCategory::all()->values(),
            'statuses' => SongStatus::all()->values(),
            'pitches' => Song::PITCHES,
        ]);
    }

    public function store(SongRequest $request): RedirectResponse
    {
        $song = Song::create($request->safe()->except('send_notification'));

        if ($request->input('send_notification')) {
            Notification::send(Membership::active()->with('user')->get()->pluck('user'), new SongUploaded($song));
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
            'members' => function (Builder $query) use ($song) {
                $query->active();
            },
            'members as performance_ready_count' => function (Builder $query) use ($song) {
                $query
                    ->active()
                    ->with('songs')
                    ->whereHas('songs', function (Builder $query) use ($song) {
                        $query->where('songs.id', $song->id)
                            ->where('membership_song.status', 'performance-ready');
                    });
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
        ]);
    }

    public function update(SongRequest $request, Song $song): RedirectResponse
    {
        $song->update($request->safe()->except('send_notification'));

        if ($request->input('send_notification')) {
            Notification::send(Membership::active()->with('user')->get()->pluck('user'), new SongUpdated($song));
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

    private function getSongs(bool $includePending, array $defaultStatuses, bool $includeNonAuditionSongs, array $showForProspectsDefault): array|Collection
    {
        return QueryBuilder::for(Song::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('status.id')
                    ->ignore($includePending ? [] : [SongStatus::where('title', '=', 'Pending')->value('id')])
                    ->default($defaultStatuses),
                AllowedFilter::callback('show_for_prospects', fn(Builder $query, $value) => $query->whereIn('show_for_prospects', $includeNonAuditionSongs ? $value : [true])
                )
                    ->default($showForProspectsDefault),
                AllowedFilter::exact('categories.id'),
            ])
            ->defaultSort('title')
            ->allowedSorts([
                'title',
                'created_at',
                AllowedSort::custom('status-title', new SongStatusSort(), 'title'),
            ])
            ->get();
    }
}
