<?php

namespace App\Http\Controllers;

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

class SongController extends Controller
{
	public function index(Request $request): InertiaResponse|View
    {
		$this->authorize('viewAny', Song::class);

		// Base query
		$songs = Song::withCount(['attachments'])
			->filter()
			->get();

		// Sort
		$sort_by = $request->input('sort_by', 'title');
		$sort_dir = $request->input('sort_dir', 'asc');

		// Flip direction for date (so we sort by smallest age not smallest timestamp)
		if ($sort_by === 'created_at') {
			$sort_dir = $sort_dir === 'asc' ? 'desc' : 'asc';
		}

		if ($sort_dir === 'asc') {
			$songs = $songs->sortBy($sort_by);
		} else {
			$songs = $songs->sortByDesc($sort_by);
		}

        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Songs/Index', [
                'songs' => $songs->values(),
            ]);
        }

		return view('songs.index', [
			'all_songs' => $songs,
			'active_songs' => $songs->where('status.title', '=', 'Active'),
			'learning_songs' => $songs->where('status.title', '=', 'Learning'),
			'pending_songs' => $songs->where('status.title', '=', 'Pending'),
			'archived_songs' => $songs->where('status.title', '=', 'Archived'),
			'filters' => Song::getFilters(),
			'sorts' => $this->getSorts($request),
		]);
	}

	public function learning(Request $request): View
	{
		$this->authorize('viewAny', Song::class);

		// Base query
		$songs = Song::with(['attachments'])
			->filter()
			->get();

		// Sort
		$sort_by = $request->input('sort_by', 'name');
		$sort_dir = $request->input('sort_dir', 'asc');

		// Flip direction for date (so we sort by smallest age not smallest timestamp)
		if ($sort_by === 'created_at') {
			$sort_dir = $sort_dir === 'asc' ? 'desc' : 'asc';
		}

		if ($sort_dir === 'asc') {
			$songs = $songs->sortBy($sort_by);
		} else {
			$songs = $songs->sortByDesc($sort_by);
		}

		$sorts = $this->getSorts($request);

		$filters = Song::getFilters();
		return view('songs.learning', compact('songs', 'filters', 'sorts'));
	}

	public function create(): View|InertiaResponse
	{
		$this->authorize('create', Song::class);

		$categories = SongCategory::all();
		$statuses = SongStatus::all();
		$pitches = Song::KEYS;

        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Songs/Create', [
                'categories' => $categories->values(),
                'statuses' => $statuses->values(),
                'pitches' => Song::PITCHES,
            ]);
        }

		return view('songs.create', compact('categories', 'statuses', 'pitches'));
	}

	public function store(SongRequest $request): RedirectResponse
	{
		$this->authorize('create', Song::class);


		$song = Song::create(
			collect($request->validated())
				->toArray(),
		);

		if($request->input('send_notification')) {
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

		$attachment_categories = SongAttachmentCategory::all();
		$categories_keyed = $attachment_categories->mapWithKeys(function ($item) {
			return [$item['id'] => $item['title']];
		});

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
                }
            ])->get();

        $song->can = [
            'update_song' => auth()->user()?->can('update', $song),
            'delete_song' => auth()->user()?->can('delete', $song),
        ];

        $song->append('my_learning');

        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Songs/Show', [
                'song' => $song,
                'all_attachment_categories' => $attachment_categories,
                'attachment_categories' => $song->attachments->mapToGroups(function($attachment) {
                    return [$attachment->category->title => $attachment];
                })->sortBy(function($attachments, $category_title) {
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

		return view('songs.show', [
		    'song' => $song,
            'categories_keyed' => $categories_keyed,
            'singers_learning_count' => Singer::active()->count() - $assessment_ready_count - $performance_ready_count,
            'singers_assessment_ready_count' => $assessment_ready_count,
            'singers_performance_ready_count' => $performance_ready_count,
            'voice_parts_performance_ready_count' => $voice_parts_performance_ready_count,
        ]);
	}

	public function edit(Song $song): View|InertiaResponse
	{
		$this->authorize('update', $song);

		$categories = SongCategory::all();
		$statuses = SongStatus::all();
		$pitches = Song::KEYS;

        if(config('features.rebuild')) {
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Songs/Edit', [
                'categories' => $categories->values(),
                'statuses' => $statuses->values(),
                'pitches' => Song::PITCHES,
                'song' => $song,
            ]);
        }

		return view('songs.edit', compact('song', 'categories', 'statuses', 'pitches'));
	}

	public function update(SongRequest $request, Song $song): RedirectResponse
	{
		$this->authorize('update', $song);

		$song->update(
			collect($request->validated())
				->except('send_notification')
				->toArray(),
		);

        if($request->input('send_notification')) {
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

	public function getSorts(Request $request): array
	{
		$sort_cols = ['title', 'status.title', 'created_at'];

		// Merge filters with sort query string
		$url = $request->url() . '?' . Song::getFilterQueryString();

		$current_sort = $request->input('sort_by', 'title');
		$current_dir = $request->input('sort_dir', 'asc');

		$sorts = [];
		foreach ($sort_cols as $col) {
			// If current sort
			if ($col === $current_sort) {
				// Create link for opposite sort direction
				$current = true;
				$dir = 'asc' === $current_dir ? 'desc' : 'asc';
			} else {
				$current = false;
				$dir = 'asc';
			}
			$sorts[$col] = [
				'url' => $url . "&sort_by=$col&sort_dir=$dir",
				'dir' => $current_dir,
				'current' => $current,
			];
		}
		return $sorts;
	}
}
