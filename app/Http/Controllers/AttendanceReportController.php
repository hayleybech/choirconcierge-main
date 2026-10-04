<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceReportController extends Controller
{
    private const PRESENT_RESPONSES = ['present', 'late'];

    public function __invoke(): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $defaultEventType = EventType::where('title', 'Performance')->value('id');
        $defaultStartsAfter = now()->subYear();
        $defaultStartsBefore = now();

        $events = QueryBuilder::for(Event::class)
            ->with([])
            ->allowedFilters([
                AllowedFilter::exact('type.id')
                    ->default([$defaultEventType]),
                AllowedFilter::scope('starts_after')
                    ->default($defaultStartsAfter),
                AllowedFilter::scope('starts_before')
                    ->default($defaultStartsBefore),
                AllowedFilter::callback('enrolments.voice_part_id', fn ($query, $value) => $query),
            ])
            ->orderBy('start_date')
            ->get();

        $voicePartIds = request()->input('filter')['enrolments.voice_part_id'] ?? null;
        $voicePartIds = $voicePartIds === null ? null : (array) $voicePartIds;
        $singers = $this->sortSingers($this->getSingers($events, $voicePartIds));

        return Inertia::render('Events/AttendanceReport', [
            'singers' => $singers->values(),
            'events' => $events->values(),
            'eventTypes' => EventType::all()->values(),
            'voiceParts' => VoicePart::all()->values(),
            'defaultEventType' => $defaultEventType,
            'defaultStartsAfter' => $defaultStartsAfter,
            'defaultStartsBefore' => $defaultStartsBefore,
            'numSingers' => $singers->count(),
            'avgSingersPerEvent' => $events->count() > 0
                ? round($events->sum('singersPresent') / $events->count(), 2)
                : null,
            'avgEventsPerSinger' => $singers->count() > 0
                ? round($singers->sum('timesPresent') / $singers->count(), 2)
                : null,
        ]);
    }

    private function sortSingers(Collection $singers): Collection
    {
        $sort = ltrim((string) request('sort', 'full-name'), '-');
        $descending = str_starts_with((string) request('sort', 'full-name'), '-');

        $compare = function (mixed $first, mixed $second) use ($descending): int {
            $result = is_numeric($first) && is_numeric($second)
                ? $first <=> $second
                : strnatcasecmp((string) $first, (string) $second);

            return $descending ? -$result : $result;
        };

        $sorted = $singers->sortBy([
            function (Membership $first, Membership $second) use ($sort, $compare): int {
                $firstValue = match ($sort) {
                    'last-name-first' => $first->user->last_name,
                    'voice-part' => $first->enrolments->first()?->voice_part?->title,
                    'attendance' => $first->percentPresent,
                    default => $first->user->first_name,
                };
                $secondValue = match ($sort) {
                    'last-name-first' => $second->user->last_name,
                    'voice-part' => $second->enrolments->first()?->voice_part?->title,
                    'attendance' => $second->percentPresent,
                    default => $second->user->first_name,
                };

                return $compare($firstValue, $secondValue);
            },
            ...in_array($sort, ['voice-part', 'attendance'], true)
                ? [fn (Membership $first, Membership $second): int => $compare(
                    $first->user->first_name,
                    $second->user->first_name,
                )]
                : [],
        ]);

        return $sorted->values();
    }

    /**
     * Determine which singers are considered for each event, then calculate totals for events and singers.
     *
     * Before membership status history was tracked, a singer is considered for an event if their attendance
     * was recorded for it. Afterwards, a singer is considered if they were an active member at the time.
     */
    private function getSingers(Collection $events, ?array $voicePartIds = null): Collection
    {
        $memberships = Membership::with([
            'user',
            'enrolments.voice_part',
            'statuses',
            'attendances' => fn($query) => $query->whereIn('event_id', $events->pluck('id')),
        ])
            ->when($voicePartIds !== null, fn ($query) => $query->whereHas(
                'enrolments',
                fn ($query) => $query->whereIn('voice_part_id', $voicePartIds),
            ))
            ->get();

        $events->each(function (Event $event) use ($memberships) {
            $event->isBeforeHistory = $event->isBeforeMembershipHistory();

            $considered = $memberships->filter(fn(Membership $singer) => $singer->isConsideredForEvent($event));

            $event->consideredSingerIds = $considered->pluck('id')->values();
            $event->numSingers = $considered->count();
            $event->singersPresent = $considered
                ->filter(fn(Membership $singer) => $singer->attendances
                    ->where('event_id', $event->id)
                    ->whereIn('response', self::PRESENT_RESPONSES)
                    ->isNotEmpty())
                ->count();
            $attendanceSummary = [
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'unknown' => 0,
            ];
            $considered->each(function (Membership $singer) use ($event, &$attendanceSummary): void {
                $response = $singer->attendances->firstWhere('event_id', $event->id)?->response;
                $response = match ($response) {
                    'present' => 'present',
                    'late' => 'late',
                    'absent', 'late_deemed_absent' => 'absent',
                    default => 'unknown',
                };

                $attendanceSummary[$response]++;
            });
            $event->attendanceSummary = $attendanceSummary;
            $event->percentPresent = $event->numSingers > 0
                ? floor($event->singersPresent / $event->numSingers * 100)
                : null;
        });

        return $memberships
            ->filter(fn(Membership $singer) => $events->contains(
                fn(Event $event) => $event->consideredSingerIds->contains($singer->id)
            ))
            ->values()
            ->makeHidden('statuses')
            ->append('user_avatar_thumb_url')
            ->each(function (Membership $singer) use ($events) {
                $consideredEventIds = $events
                    ->filter(fn(Event $event) => $event->consideredSingerIds->contains($singer->id))
                    ->pluck('id');

                $singer->numEvents = $consideredEventIds->count();
                $singer->timesPresent = $singer
                    ->attendances
                    ->whereIn('event_id', $consideredEventIds)
                    ->whereIn('response', self::PRESENT_RESPONSES)
                    ->count();
                $singer->percentPresent = $singer->numEvents > 0
                    ? floor($singer->timesPresent / $singer->numEvents * 100)
                    : null;
            });
    }
}
