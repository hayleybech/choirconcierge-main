<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Enrolment;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\Membership;
use App\Models\VoicePart;
use App\Models\SingerStatus;
use App\Traits\HasSingerSorts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceController extends Controller
{
    use HasSingerSorts;

    public function index(Event $event): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $event->createMissingAttendanceRecords();

        $attendanceSort = AllowedSort::callback('attendance-response', function (Builder $query, bool $descending) use ($event) {
            $direction = $descending ? 'DESC' : 'ASC';
            $query->select('memberships.*')
                ->selectSub(
                    Attendance::query()
                        ->select('response')
                        ->whereColumn('membership_id', 'memberships.id')
                        ->where('event_id', $event->id)
                        ->limit(1),
                    'attendance_response'
                )
                ->orderByRaw("CASE
                    WHEN attendance_response = 'present' THEN 1
                    WHEN attendance_response = 'late' THEN 2
                    WHEN attendance_response = 'late_deemed_absent' THEN 3
                    WHEN attendance_response = 'absent' THEN 4
                    WHEN attendance_response = 'absent_apology' THEN 5
                    WHEN attendance_response = 'unknown' THEN 6
                    WHEN attendance_response IS NULL THEN 6
                    ELSE 7
                END $direction");
        });
        $updatedSort = AllowedSort::callback('attendance-updated', function (Builder $query, bool $descending) use ($event) {
            $direction = $descending ? 'DESC' : 'ASC';
            $query->select('memberships.*')
                ->selectSub(
                    Attendance::query()
                        ->select('updated_at')
                        ->whereColumn('membership_id', 'memberships.id')
                        ->where('event_id', $event->id)
                        ->limit(1),
                    'attendance_updated'
                )
                ->orderBy('attendance_updated', $direction);
        });

        $defaultStatusId = SingerStatus::where('name', 'Members')->value('id');
        $filter = request()->query('filter', []);

        $query = Membership::forEvent($event)
            ->with([
                'user',
                'enrolments.voice_part',
                'enrolments.ensemble',
                'status',
                'attendances' => fn($query) => $query->where('event_id', '=', $event->id),
            ])
            ->when($defaultStatusId && !isset($filter['status.id']), function (Builder $query) use ($defaultStatusId) {
                $query->whereHas('status', fn($q) => $q->where('singer_statuses.id', $defaultStatusId)
                    ->where('membership_singer_status.id', function($sub) {
                        $sub->selectRaw('max(id)')
                            ->from('membership_singer_status')
                            ->whereColumn('membership_id', 'memberships.id');
                    })
                );
            });

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
                AllowedFilter::callback('attendance.response', function (Builder $query, $value) use ($event) {
                    $responses = (array) $value;
                    $query->whereHas('attendances', fn($query) => $query->where('event_id', $event->id)->whereIn('response', $responses));
                }),
                AllowedFilter::callback('status.id', fn($query, $value) => $query->whereHas('status', fn($q) => $q
                    ->whereIn('singer_statuses.id', (array)$value)
                    ->where('membership_singer_status.id', function($sub) {
                        $sub->selectRaw('max(id)')
                            ->from('membership_singer_status')
                            ->whereColumn('membership_id', 'memberships.id');
                    })
                )),
            ])
            ->allowedSorts([
                ...$this->singerSorts(),
                $attendanceSort,
                $updatedSort,
            ])
            ->defaultSort($this->singerSorts()[0])
            ->paginate(50)
            ->appends(request()->query());

        $singers = collect($pagination->items())->map(function ($membership) use ($event) {
            $membership->attendance = $membership->attendances->first() ?? Attendance::Null();
            $membership->user->append('avatar_url');

            if ($event->ensembles->isNotEmpty()) {
                $membership->setRelation('enrolments', $membership->enrolments->filter(function ($enrolment) use ($event) {
                    return $event->ensembles->contains($enrolment->ensemble_id);
                }));
            }

            return $membership;
        });

        return Inertia::render('Events/Attendance/Index', [
            'event' => $event->load('ensembles'),
            'allSingers' => $singers,
            'pagination' => $pagination,
            'individualCheckInUrl' => $this->getCheckInUrl($event),
            'voiceParts' => VoicePart::all()->values(),
            'ensembles' => Ensemble::ensembleRestricted()->get()->values(),
            'totalEnsemblesCount' => Ensemble::count(),
            'singerStatuses' => SingerStatus::all()->values(),
            'counts' => [
                'present' => $event->attendances()->where('response', 'present')->count(),
                'late' => $event->attendances()->where('response', 'late')->count(),
                'absent' => $event->attendances()->whereIn('response', ['absent', 'absent_apology', 'late_deemed_absent'])->count(),
                'unknown' => $event->attendances()->where('response', 'unknown')->count(),
            ],
        ]);
    }

    private function getCheckInUrl(Event $event): string
    {
        if (!auth()->user()->can('create', Attendance::class)) {
            return '';
        }

        if ($event->is_repeating) {
            return URL::temporarySignedRoute('events.check-ins.index', $event->repeat_until, ['event' => $event->repeat_parent_id]);
        }

        return URL::temporarySignedRoute('events.check-ins.index', $event->end_date, ['event' => $event]);
    }

    public function update(Event $event, Membership $singer, Request $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $request->validate([
            'response' => ['in:unknown,absent,absent_apology,late,late_deemed_absent,present'],
        ]);

        $event->attendances()
            ->updateOrCreate(
                ['membership_id' => $singer->id],
                [
                    'response' => $request->input('response'),
                    'absent_reason' => $request->input('absent_reason'),
                    'source' => 'manual',
                ]
            );

        return redirect()
            ->route('events.attendances.index', ['event' => $event])
            ->with(['status' => 'Attendance recorded.']);
    }

    public function updateAll(Event $event, Request $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $absent_reason = $request->input('absent_reason');
        $responses = $request->input('attendance_response');
        foreach ($responses as $membership_id => $response) {
            $event->attendances()->updateOrCreate(
                ['membership_id' => $membership_id],
                [
                    'response' => $response,
                    'absent_reason' => $absent_reason[$membership_id],
                    'source' => 'manual',
                ],
            );
        }

        return redirect()
            ->route('events.show', ['event' => $event])
            ->with(['status' => 'Attendance recorded.']);
    }
    public function bulkUpdate(Event $event, Request $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $request->validate([
            'singer_ids' => ['required', 'array'],
            'singer_ids.*' => ['exists:memberships,id'],
            'response' => ['required', 'in:unknown,absent,absent_apology,late,late_deemed_absent,present'],
        ]);

        $singerIds = $request->input('singer_ids');
        $response = $request->input('response');

        foreach ($singerIds as $singerId) {
            $event->attendances()->updateOrCreate(
                ['membership_id' => $singerId],
                [
                    'response' => $response,
                    'source' => 'manual',
                ]
            );
        }

        return redirect()
            ->route('events.attendances.index', ['event' => $event])
            ->with(['status' => 'Attendance updated for ' . count($singerIds) . ' singers.']);
    }
}
