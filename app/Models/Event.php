<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\CalendarLinks\Exceptions\InvalidLink;
use Spatie\CalendarLinks\Link;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Event
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property int $type_id
 * @property Carbon $call_time
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $location_place_id
 * @property string $location_icon
 * @property string $location_name
 * @property string $location_address
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Columns - Repeating events
 * @property bool $is_repeating
 * @property int $repeat_parent_id
 * @property Carbon $repeat_until
 * @property int $repeat_frequency_amount e.g. 2 (day) - fortnightly
 * @property string $repeat_frequency_unit (day, week, month, year)
 *
 * Attributes
 * @property bool $in_past
 * @property bool $in_future
 * @property bool $is_repeat_parent
 * @property bool $parent_in_past
 * @property Rsvp $my_rsvp
 *
 * Relationships
 * @property EventType $type
 * @property Collection<Rsvp> $rsvps
 * @property Attendance $my_attendance
 * @property Collection<Attendance> $attendances
 * @property Collection<EventActivity> $activities
 *
 * Relationships - Repeating Events
 * @property Event $repeat_parent
 * @property Collection<Event> $repeat_children
 */
class Event extends Model
{
    use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'call_time',
        'location_place_id',
        'location_name',
        'location_address',
        'description',
        'type_id',

        'is_repeating',
        'repeat_parent_id',
        'repeat_until',
        'repeat_frequency_amount',
        'repeat_frequency_unit',
        'created_at',
        'updated_at',
    ];

    protected $with = ['type'];

    public $dates = ['updated_at', 'created_at', 'start_date', 'end_date', 'call_time', 'repeat_until', 'deleted_at'];

    protected $casts = [
        'is_repeating' => 'boolean',
    ];

    private ?Link $_add_to_calendar_link;

    protected static function booted(): void
    {
        static::created(static function (self $event) {
            $event->createRepeats();
        });
    }

    /**
     * Generates occurrences for a repeating event
     * - Starts at the date of the second occurrence
     * - Increments the date by the frequency
     * - Loops until date is after the repeat end date
     * - Doesn't yet support repeat_frequency_amount (always = 1 for now)
     * - Doesn't yet differentiate between e.g. "every month on the 18th" and "every month on the 3rd thursday" and "every 30 days"
     */
    public function createRepeats(): void
    {
        if (! $this->is_repeating) {
            return;
        }

        $this->repeat_parent_id = $this->id;

        // temporary fix? repeat_until shouldn't ask for hours/min
        $this->repeat_until = $this->repeat_until->setHours($this->call_time->hour);
        $this->repeat_until = $this->repeat_until->setMinutes($this->call_time->minute);

        $this->save();

        $second_event_call_time = $this->call_time->copy()->add($this->repeat_frequency_unit, 1);
        $second_event_start_date = $this->start_date->copy()->add($this->repeat_frequency_unit, 1);
        $second_event_end_date = $this->end_date->copy()->add($this->repeat_frequency_unit, 1);

        $mysql_date_format = 'Y-m-d H:i:s';

        $event_occurrences = [];
        for (
            $current_event_call_time = $second_event_call_time,
            $current_start_date = $second_event_start_date,
            $current_event_end_date = $second_event_end_date;
            $current_event_call_time <= $this->repeat_until;
            $current_event_call_time->add($this->repeat_frequency_unit, 1),
            $current_start_date->add($this->repeat_frequency_unit, 1),
            $current_event_end_date->add($this->repeat_frequency_unit, 1)
        ) {
            $event_occurrences[] = $this->replicate()
                ->fill([
                    'start_date' => tz_from_tenant_to_utc($current_start_date->toString())->format($mysql_date_format),
                    'end_date' => tz_from_tenant_to_utc($current_event_end_date->toString())->format(
                        $mysql_date_format,
                    ),
                    'call_time' => tz_from_tenant_to_utc($current_event_call_time->toString())->format(
                        $mysql_date_format,
                    ),
                    'repeat_until' => tz_from_tenant_to_utc($this->repeat_until->toString())->format(
                        $mysql_date_format,
                    ),
                    'created_at' => Carbon::now()->format($mysql_date_format),
                    'updated_at' => Carbon::now()->format($mysql_date_format),
                ])
                ->attributesToArray();
        }
        DB::table('events')->insert($event_occurrences);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(EventActivity::class);
    }

    public function repeat_parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'repeat_parent_id');
    }

    public function repeat_children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'repeat_parent_id')->whereColumn('id', '!=', 'repeat_parent_id');
    }

    public function repeat_siblings(): Builder
    {
        return self::query()
            ->where('id', '!=', $this->id)
            ->where('repeat_parent_id', '=', $this->repeat_parent_id);
    }

    public function nextRepeats(): Builder
    {
        return $this->repeat_siblings()->whereDate('start_date', '>', $this->start_date);
    }

    public function nextRepeat(): self|Model|null
    {
        return $this->nextRepeats()
            ->orderBy('start_date')
            ->first();
    }

    public function prevRepeats(): Builder
    {
        return $this->repeat_siblings()->whereDate('start_date', '<', $this->start_date);
    }

    public function prevRepeat(): self|Model|null
    {
        return $this->prevRepeats()
            ->orderBy('start_date', 'desc')
            ->first();
    }

    public function my_attendance(): HasOne
    {
        return $this->hasOne(Attendance::class)
            ->where(
                'singer_id',
                '=',
                auth()->user()->singer?->id ?? 0,
            )
            ->withDefault(['response' => 'unknown']);
    }

    public function singers_rsvp_response(string $response): Builder
    {
        return Singer::active()->whereHas('rsvps', function (Builder $query) use ($response) {
            $query->where('event_id', '=', $this->id)->where('response', '=', $response);
        });
    }

    public function voice_parts_rsvp_response_count(string $response)
    {
        return VoicePart::withCount([
            'singers' => function ($query) {
                $query->active();
            },
            'singers as singers_going_count' => function ($query) use ($response) {
                $query->active()->whereHas('rsvps', function (Builder $query) use ($response) {
                    $query->where('event_id', '=', $this->id)
                        ->where('response', '=', $response);
                });
            }, ]);
    }

    public function singers_rsvp_missing(): Builder
    {
        return Singer::active()->whereDoesntHave('rsvps', function (Builder $query) {
            $query->where('event_id', '=', $this->id);
        });
    }

    public function singers_attendance(string $response): Builder
    {
        return Singer::active()->whereHas('attendances', function (Builder $query) use ($response) {
            $query->where('event_id', '=', $this->id)->where('response', '=', $response);
        });
    }

    public function singers_attendance_missing(): Builder
    {
        return Singer::active()->whereDoesntHave('attendances', function (Builder $query) {
            $query->where('event_id', '=', $this->id);
        });
    }

    public function voice_parts_attendance_count(string $response)
    {
        return VoicePart::withCount([
            'singers' => function ($query) use ($response) {
                $query->active();
            },
            'singers as singers_response_count' => function ($query) use ($response) {
                $query->active()->whereHas('attendances', function (Builder $query) use ($response) {
                    $query->where('event_id', '=', $this->id)
                        ->where('response', '=', $response);
                });
            },
        ]);
    }

    public function getStartDateAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }

    public function setStartDateAttribute(string $value): void
    {
        $this->attributes['start_date'] = tz_from_tenant_to_utc($value);
    }

    public function getEndDateAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }

    public function setEndDateAttribute(string $value): void
    {
        $this->attributes['end_date'] = tz_from_tenant_to_utc($value);
    }

    public function getCallTimeAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }

    public function setCallTimeAttribute(string $value): void
    {
        $this->attributes['call_time'] = tz_from_tenant_to_utc($value);
    }

    public function getRepeatUntilAttribute(?string $value): ?Carbon
    {
        return $value ? tz_from_utc_to_tenant($value) : null;
    }

    public function setRepeatUntilAttribute(string $value): void
    {
        $this->attributes['repeat_until'] = tz_from_tenant_to_utc($value);
    }

    public function getInPastAttribute(): bool
    {
        return $this->call_time < Carbon::now();
    }

    public function getInFutureAttribute(): bool
    {
        return $this->call_time > Carbon::now();
    }

    public function getIsRepeatParentAttribute(): bool
    {
        return $this->repeat_parent_id === $this->id;
    }

    public function getParentInPastAttribute(): ?bool
    {
        return $this->repeat_parent?->in_past ?? null;
    }

    public function getAddToCalendarLinkAttribute(): ?Link
    {
        if (! isset($this->_add_to_calendar_link)) {
            try {
                $this->_add_to_calendar_link = Link::create($this->title, $this->call_time, $this->end_date)
                    ->description($this->description ?? '')
                    ->address($this->location_address ?? '');
            } catch(InvalidLink $e) {
                $this->_add_to_calendar_link = null;
                \Sentry::captureException($e);
            }
        }

        return $this->_add_to_calendar_link;
    }

    public function getMyRsvpAttribute(): Rsvp|Model
    {
        if(! auth()->check() || ! auth()->user()->singer) {
            return Rsvp::Null();
        }

        return $this->rsvps()
            ->where('singer_id', '=', auth()->user()->singer->id)
            ->first() ?? Rsvp::Null();
    }

    /**
     * Checks if any of the repeat data has changed - including call/start time
     * @todo allow changing the time (not the date) without causing series regeneration
     */
    public function isRepeatDirty(): bool
    {
        return $this->isDirty([
            'call_time',
            'start_date',
            'repeat_until',
            'repeat_frequency_unit',
            'repeat_frequency_amount',
        ]);
    }

    public function createMissingAttendanceRecords(): void
    {
        $this->attendances()->createMany(
            Singer::active()
                ->whereDoesntHave('attendances', fn ($query) => $query->where('attendances.event_id', $this->id))
                ->pluck('id')
                ->map(fn ($singerId) => ['singer_id' => $singerId, 'response' => 'unknown'])
        );
    }

    public function scopeDate(Builder $query, string $mode = 'upcoming', Carbon $date = null, Carbon $date2 = null): Builder
    {
        return match ($mode) {
            'after' => $query->where('call_time', '>=', $date),
            'upcoming' => $query->where('call_time', '>=', Carbon::today()),
            'before' => $query->where('call_time', '<=', $date),
            'past' => $query->where('call_time', '<=', Carbon::today()),
            'between' => $query->where('call_time', '>=', $date)->where('call_time', '<=', $date2)
        };
    }
}
