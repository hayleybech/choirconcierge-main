<?php

namespace App\Http\Controllers;

use App\CustomSorts\SongStatusSort;
use App\Http\Requests\SongRequest;
use App\Models\Singer;
use App\Models\Song;
use App\Models\SongAttachmentCategory;
use App\Models\SongCategory;
use App\Models\SongStatus;
use App\Models\VoicePart;
use App\Notifications\SongUpdated;
use App\Notifications\SongUploaded;
use Illuminate\Contracts\View\View;
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
    public function index(Request $request): InertiaResponse|View
    {
        $this->authorize('viewAny', Song::class);

        $includePending = auth()->user()?->singer?->hasAbility('songs_update');
        $statuses = SongStatus::query()
            ->when(! $includePending, fn ($query) => $query->where('title', '!=', 'Pending'))
            ->get();
        $defaultStatuses = $statuses->where('title', '!=', 'Archived')->pluck('id')->toArray();

        $includeNonAuditionSongs = auth()->user()?->singer?->category->name === 'Members';
        $showForProspectsDefault = $includeNonAuditionSongs ? [false, true] : [true];

        $songs = QueryBuilder::for(Song::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('status.id')
                    ->ignore($includePending ? [] : [SongStatus::where('title', '=', 'Pending')->value('id')])
                    ->default($defaultStatuses),
                AllowedFilter::callback('show_for_prospects', fn (Builder $query, $value) => $query->whereIn('show_for_prospects', $includeNonAuditionSongs ? $value : [true])
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

        return Inertia::render('Songs/Index', [
            'songs' => $songs->values(),
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
        $this->authorize('create', Song::class);

        return Inertia::render('Songs/Create', [
            'categories' => SongCategory::all()->values(),
            'statuses' => SongStatus::all()->values(),
            'pitches' => Song::PITCHES,
        ]);
    }

    public function store(SongRequest $request): RedirectResponse
    {
        $this->authorize('create', Song::class);

        $song = Song::create(
            collect($request->validated())
                ->toArray(),
        );

        if ($request->input('send_notification')) {
            Notification::send(Singer::active()->with('user')->get()->pluck('user'), new SongUploaded($song));
        }

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Song created. ']);
    }

    public function show(Song $song): View|InertiaResponse
    {
        $this->authorize('view', $song);

        $song->load('attachments.category');

        $assessment_ready_count = $song->singers()->active()->wherePivot('status', 'assessment-ready')->count();
        $performance_ready_count = $song->singers()->active()->wherePivot('status', 'performance-ready')->count();

        $voice_parts_performance_ready_count = VoicePart::withCount([
            'singers' => function (Builder $query) use ($song) {
                $query->active();
            },
            'singers as performance_ready_count' => function (Builder $query) use ($song) {
                $query
                        ->active()
                        ->with('songs')
                        ->whereHas('songs', function (Builder $query) use ($song) {
                            $query->where('songs.id', $song->id)
                                ->where('singer_song.status', 'performance-ready');
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
            'all_attachment_categories' => SongAttachmentCategory::all(),
            'attachment_categories' => $song->attachments->mapToGroups(function ($attachment) {
                return [$attachment->category->title => $attachment];
            })->sortBy(function ($attachments, $category_title) {
                return match ($category_title) {
                    'Sheet Music' => 0,
                    'Full Mix (Demo)' => 1,
                    'Learning Tracks' => 2,
                    'Other' => 3,
                };
            }),
            'status_count' => [
                'performance_ready' => $performance_ready_count,
                'assessment_ready' => $assessment_ready_count,
                'learning' => Singer::active()->count() - $assessment_ready_count - $performance_ready_count,
            ],
            'voice_parts_count' => [
                'performance_ready' => $voice_parts_performance_ready_count,
            ],
        ]);
    }

    public function edit(Song $song): View|InertiaResponse
    {
        $this->authorize('update', $song);

        return Inertia::render('Songs/Edit', [
            'categories' => SongCategory::all()->values(),
            'statuses' => SongStatus::all()->values(),
            'pitches' => Song::PITCHES,
            'song' => $song,
        ]);
    }

    public function update(SongRequest $request, Song $song): RedirectResponse
    {
        $this->authorize('update', $song);

        $song->update(
            collect($request->validated())
                ->except('send_notification')
                ->toArray(),
        );

        if ($request->input('send_notification')) {
            Notification::send(Singer::active()->with('user')->get()->pluck('user'), new SongUpdated($song));
        }

        return redirect()
            ->route('songs.show', [$song])
            ->with(['status' => 'Song updated. ']);
    }

    public function destroy(Song $song): RedirectResponse
    {
        $this->authorize('delete', $song);

        $song->delete();

        return redirect()
            ->route('songs.index')
            ->with(['status' => 'Song deleted. ']);
    }
}
