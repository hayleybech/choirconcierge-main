<?php

namespace App\Http\Controllers;

use App\Enums\SingerStatus;
use App\Models\Ensemble;
use App\Models\LearningStatus as LearningStatusPivot;
use App\Models\Membership;
use App\Models\Song;
use App\Models\VoicePart;
use App\Traits\HasSingerSorts;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class LearningStatusController extends Controller
{
    use HasSingerSorts;

    public function index(Song $song): View|InertiaResponse
    {
        $this->authorize('update', $song);

        $song->createMissingLearningRecords();

        $query = Membership::query()
            ->whereHas('songs', fn(Builder $query) => $query->where('songs.id', $song->id))
            ->with([
                'user',
                'enrolments.voice_part',
                'enrolments.ensemble',
                'status',
                'songs' => fn($query) => $query->where('songs.id', $song->id),
            ]);

        $pagination = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::callback('user.name', fn(Builder $query, $value) => $query
                    ->whereHas('user', fn(Builder $query) => $query
                        ->whereRaw('CONCAT(first_name, ?, last_name) LIKE LOWER(?)', [' ', "%$value%"])
                        ->orWhereRaw('email LIKE LOWER(?)', ["%$value%"])
                    )),
                AllowedFilter::callback('enrolments.voice_part_id', fn(Builder $query, $value) => $query
                    ->whereHas('enrolments', fn(Builder $query) => $query
                        ->whereIn('voice_part_id', (array) $value)
                    )
                ),
                AllowedFilter::callback('enrolments.ensemble_id', fn(Builder $query, $value) => $query
                    ->whereHas('enrolments', fn(Builder $query) => $query
                        ->whereIn('ensemble_id', (array) $value)
                    )
                ),
                AllowedFilter::callback('learning.status', function (Builder $query, $value) use ($song) {
                    $statuses = (array) $value;
                    $query->whereHas('songs', function (Builder $query) use ($song, $statuses) {
                        $query->where('songs.id', $song->id)
                            ->whereIn('membership_song.status', $statuses);
                    });
                }),
                AllowedFilter::callback('status.id', function (Builder $query, $value) {
                    $query->whereHas('status', fn($q) => $q
                        ->whereIn('status', (array) $value)
                    );
                })->default([SingerStatus::MEMBERS->value]),
            ])
            ->allowedSorts([
                ...$this->singerSorts(),
                AllowedSort::callback('learning-status', function (Builder $query, bool $descending) use ($song) {
                    $direction = $descending ? 'DESC' : 'ASC';
                    $query->select('memberships.*')
                        ->selectSub(
                            DB::table('membership_song')
                                ->select('status')
                                ->whereColumn('membership_id', 'memberships.id')
                                ->where('song_id', $song->id)
                                ->limit(1),
                            'learning_status'
                        )
                        ->orderByRaw("CASE
                            WHEN learning_status = 'performance-ready' THEN 1
                            WHEN learning_status = 'assessment-ready' THEN 2
                            WHEN learning_status = 'not-started' THEN 3
                            WHEN learning_status IS NULL THEN 4
                            ELSE 5
                        END $direction");
                }),
                AllowedSort::callback('learning-updated', function (Builder $query, bool $descending) use ($song) {
                    $direction = $descending ? 'DESC' : 'ASC';
                    $query->select('memberships.*')
                        ->selectSub(
                            DB::table('membership_song')
                                ->select('updated_at')
                                ->whereColumn('membership_id', 'memberships.id')
                                ->where('song_id', $song->id)
                                ->limit(1),
                            'learning_updated'
                        )
                        ->orderBy('learning_updated', $direction);
                }),
            ])
            ->defaultSort($this->singerSorts()[0])
            ->paginate(50)
            ->appends(request()->query());

        $singers = $pagination->getCollection()->map(function (Membership $membership) use ($song) {
            $membership->learning = $membership->songs->first()?->learning ?? LearningStatusPivot::getNullLearningStatus();
            $membership->unsetRelation('songs');
            $membership->user->append('avatar_url');

            if ($song->ensembles->isNotEmpty()) {
                $membership->setRelation('enrolments', $membership->enrolments->filter(function ($enrolment) use ($song) {
                    return $song->ensembles->contains($enrolment->ensemble_id);
                })->values());
            }

            return $membership;
        });

        return Inertia::render('Songs/Learning/Index', [
            'song' => $song,
            'allSingers' => $singers,
            'pagination' => $pagination,
            'totalEnsemblesCount' => Ensemble::count(),
            'voiceParts' => VoicePart::all()->values(),
            'ensembles' => Ensemble::forUser()->get()->values(),
            'singerStatuses' => array_map(fn($s) => [
                'id' => $s->value,
                'name' => $s->label(),
                'slug' => $s->value,
            ], SingerStatus::cases()),
            'counts' => [
                'performance-ready' => DB::table('membership_song')->where('song_id', $song->id)->where('status', 'performance-ready')->count(),
                'assessment-ready' => DB::table('membership_song')->where('song_id', $song->id)->where('status', 'assessment-ready')->count(),
                'not-started' => DB::table('membership_song')->where('song_id', $song->id)->where('status', 'not-started')->count(),
            ],
        ]);
    }

    public function update(Song $song, Membership $singer, Request $request)
    {
        $this->authorize('update', $song);

        $song->members()->updateExistingPivot($singer->id, ['status' => $request->input('status')]);

        return redirect()->route('songs.singers.index', $song);
    }

    public function bulkUpdate(Song $song, Request $request)
    {
        $this->authorize('update', $song);

        $validated = $request->validate([
            'singer_ids' => ['required', 'array'],
            'singer_ids.*' => ['integer', 'exists:memberships,id'],
            'status' => ['required', 'in:not-started,assessment-ready,performance-ready'],
        ]);

        $song->members()
            ->whereIn('memberships.id', $validated['singer_ids'])
            ->get()
            ->each(fn (Membership $singer) => $song->members()->updateExistingPivot($singer->id, ['status' => $validated['status']]));

        return redirect()->route('songs.singers.index', $song);
    }
}
