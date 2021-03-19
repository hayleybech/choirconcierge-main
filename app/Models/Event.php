<?php

namespace App\Models;

use App\Models\Filters\Event_DateFilter;
use App\Models\Filters\Event_TypeFilter;
use App\Models\Filters\Filterable;
use App\Notifications\EventCreated;
use App\Notifications\EventUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
 *
 * Relationships
 * @property EventType $type
 * @property Collection<Rsvp> $rsvps
 * @property Collection<Attendance> $attendances
 *
 * Relationships - Repeating Events
 * @property Event $repeat_parent
 * @property Collection<Event> $repeat_children
 *
 * @package App
 */
class Event extends Model
{
    use Filterable, BelongsToTenant, SoftDeletes, HasFactory;

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

        'is_repeating',
        'repeat_parent_id',
        'repeat_until',
        'repeat_frequency_amount',
        'repeat_frequency_unit',
    ];

    protected static $filters = [
        Event_TypeFilter::class,
        Event_DateFilter::class,
    ];

    protected $with = [
        'type',
    ];

    public $dates = [
        'updated_at',
        'created_at',
        'start_date',
        'end_date',
        'call_time',
        'repeat_until',
    ];

    protected $casts = [
        'is_repeating' => 'boolean',
    ];

    private Link $_add_to_calendar_link;

    public static function create( array $attributes = [], bool $send_notification = true )
    {
        /** @var Event $event */
        $event = static::query()->create($attributes);

        $event->type_id = $attributes['type'];

        if( $send_notification ){
            Notification::send(User::active()->get(), new EventCreated($event));
        }

        $event->createRepeats();

        $event->save(); // @todo remove double save without losing data

        return $event;
    }

    /**
     * Generates occurrences for a repeating event
     * - Starts at the date of the second occurrence
     * - Increments the date by the frequency
     * - Loops until date is after the repeat end date
     * - Doesn't yet support repeat_frequency_amount (always = 1 for now)
     * - Doesn't yet differentiate between e.g. "every month on the 18th" and "every month on the 3rd thursday" and "every 30 days"
     */
    private function createRepeats(): void{
        if( ! $this->is_repeating) {
            return;
        }

        $this->repeat_parent_id = $this->id;

        // temporary fix? repeat_until shouldn't ask for hours/min
        $this->repeat_until = $this->repeat_until->setHours($this->start_date->hour);
        $this->repeat_until = $this->repeat_until->setMinutes($this->start_date->minute);

        $second_event_start_date = $this->start_date->copy()->add($this->repeat_frequency_unit, 1);
        $second_event_end_date = $this->end_date->copy()->add($this->repeat_frequency_unit, 1);
        $second_event_call_time = $this->call_time->copy()->add($this->repeat_frequency_unit, 1);

        $mysql_date_format = 'Y-m-d H:i:s';

        $event_occurrences = [];
        for($current_start_date = $second_event_start_date,
                $current_event_end_date = $second_event_end_date,
                $current_event_call_time = $second_event_call_time;
            $current_start_date <= $this->repeat_until;
            $current_start_date->add($this->repeat_frequency_unit, 1),
                $current_event_end_date->add($this->repeat_frequency_unit, 1),
                $current_event_call_time->add($this->repeat_frequency_unit, 1)
        ) {
            // save single event to array, bulk save all at the end
            $event_occurrences[] = array_merge($this->replicate()->attributesToArray(), [
                'start_date' => $current_start_date->format($mysql_date_format),
                'end_date' => $current_event_end_date->format($mysql_date_format),
                'call_time' => $current_event_call_time->format($mysql_date_format),
                'repeat_until' => $this->repeat_until->format($mysql_date_format),
                'created_at' => Carbon::now()->format($mysql_date_format),
                'updated_at' => Carbon::now()->format($mysql_date_format)
            ]);
        }
        DB::table('events')->insert($event_occurrences);
    }

    public function update(array $attributes = [], array $options = [])
    {
        $this->fill($attributes);

        $this->type_id = $attributes['type'];

        $this->updateRepeats($options['edit_mode']);

        $this->save($options);

        if( $options['send_notification'] ){
            Notification::send(User::active()->get(), new EventUpdated($this));
        }

        return true;
    }

    private function updateRepeats(?string $edit_mode): void
    {
        if ( ! $this->is_repeating) {
            return;
        }

        if($edit_mode === 'single') {
            $this->updateSingle();
        } elseif ($edit_mode === 'all') {
            $this->updateAll();
        } elseif ($edit_mode === 'following') {
            $this->updateFollowing();
        } else {
            abort(500, 'The server failed to determine the edit mode on the repeating event.');
        }
    }

    /**
     * Updates one event in a repeating series
     * For simplicity, it converts the event into regular single event.
     */
    private function updateSingle(): void {
        // If this event was the parent, reset parent id on children to next child
        if($this->is_repeat_parent && $this->repeat_children->count()){
            $new_parent = $this->nextRepeat();
            optional($new_parent)->repeat_children()->saveMany($this->repeat_children);
        }
        // Reset parent id on this event
        $this->repeat_parent_id = null;
        // Convert to single
        $this->is_repeating = false;
        // @todo allow creating new repeating events when editing a single occurrence
    }

    /**
     * Updates ALL events in a repeating series
     * When the date or repeat details change, it deletes and regenerates the entire series.
     * As a result, existing RSVPs will be deleted, but as the dates may have changed this is ideal.
     */
    private function updateAll(): void {
        // Only perform this on an event parent
        abort_if(! $this->is_repeat_parent, 500, 'The server attempted to update all repeats of an event without finding the parent event. ');

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if($this->in_past, 405, 'To protect attendance data, you cannot bulk update events in the past. Please edit individually instead.');

        // Update or regenerate children
        // Check if any of the repeat data has changed - includes start time
        // @todo allow changing the time (not the date) without causing series regeneration
        if($this->isDirty([
            'start_date',
            'repeat_until',
            'repeat_frequency_unit',
            'repeat_frequency_amount'
        ])) {
            // Delete children
            $this->repeat_children()->delete();

            // Re-create children
            $this->createRepeats();
        } else {
            // Update attributes on children
            $this->repeat_children()->update($this->getDirty());
        }
    }

    /**
     * Updates the target event and all repeats after it.
     * Makes this event the event parent for following events.
     * If repeat data (including start date) has changed, then delete and regenerate the new children.
     * Also, update the older events that still exist in the old series with new repeat_until dates.
     */
    private function updateFollowing(): void {
        // Only perform this on event children - it's too inefficient to attempt this on a parent rather than simply updateAll()
        abort_if($this->is_repeat_parent, 405, 'Cannot do "following" update method on a repeating event parent. Try "all" update method instead.');

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if($this->in_past, 405, 'To protect attendance data, you cannot bulk update events in the past. Please edit individually instead.');

        // Update prev siblings with repeat_until dates that reflect their smaller scope.
        //$this->prevRepeats()->update(['repeat_until' => $this->prevRepeat()->start_date]);

        // Update or regenerate children
        // Check if any of the repeat data has changed - includes start time
        // @todo allow changing the time (not the date) without causing series regeneration
        if($this->isDirty([
            'start_date',
            'repeat_until',
            'repeat_frequency_unit',
            'repeat_frequency_amount'
        ])) {
            // Delete all repeats following this one
            $this->nextRepeats()->delete();

            // Re-create events with this as the new parent
            $this->repeat_parent_id = $this->id;
            $this->createRepeats();
        } else {
            // Update attributes on following events and make them children
            $this->nextRepeats()->update(array_merge(
                ['repeat_parent_id' => $this->id],
                $this->getDirty()
            ));
            $this->repeat_parent_id = $this->id;
        }
    }


    public function delete_single(): bool
    {
        // Re-assign parent to the next event
        if($this->is_repeat_parent && $this->nextRepeat()) {
            $this->nextRepeats()->update(['repeat_parent_id' => $this->nextRepeat()->id]);
        }

        return $this->delete();
    }

    public function delete_with_following(): bool
    {
        // Only perform this on event children - it's too inefficient to attempt this on a parent rather than simply updateAll()
        abort_if($this->is_repeat_parent, 405, 'Cannot do "following" delete method on a repeating event parent. Try "all" delete method instead.');

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if($this->in_past, 405, 'To protect attendance data, you cannot bulk delete events in the past. Please delete individually instead.');

        // Update prev siblings with repeat_until dates that reflect their smaller scope.
        $this->prevRepeats()->update(['repeat_until' => $this->prevRepeat()->start_date]);

        // Delete all repeats following this one
        $this->nextRepeats()->delete();

        return $this->delete();
    }

    public function delete_with_all(): bool
    {
        // Only perform this on an event parent
        abort_if(! $this->is_repeat_parent, 500, 'The server attempted to delete all repeats of an event without finding the parent event. ');

        // Only perform this on events in the future - we don't want users to accidentally delete attendance data.
        abort_if($this->in_past, 405, 'To protect attendance data, you cannot bulk delete events in the past. Please delete individually instead.');

        $this->repeat_children()->delete();

        return $this->delete();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function my_rsvp()
    {
        return $this->rsvps()
            ->where('singer_id', '=', \Auth::user()->singer->id)
            ->first();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
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
        return self::query()->where('id', '!=', $this->id)
            ->where('repeat_parent_id', '=', $this->repeat_parent_id);
    }
    public function nextRepeats(): Builder
    {
        return $this->repeat_siblings()
            ->whereDate('start_date', '>', $this->start_date);
    }
    public function nextRepeat(): Event|Model|null
    {
        return $this->nextRepeats()
            ->orderBy('start_date')
            ->first();
    }
    public function prevRepeats(): Builder
    {
        return $this->repeat_siblings()
            ->whereDate('start_date', '<', $this->start_date);
    }
    public function prevRepeat(): Event|Model|null
    {
        return $this->prevRepeats()
            ->orderBy('start_date', 'desc')
            ->first();
    }

    public function my_attendance()
    {
        return $this->attendances()
            ->where('singer_id', '=', \Auth::user()->singer->id)
            ->first();
    }

    public function singers_rsvp_response(string $response): Builder
    {
        return Singer::whereHas('rsvps', function(Builder $query) use ($response) {
            $query->where('event_id', '=', $this->id)
                ->where('response', '=', $response);
        });
    }
    public function voice_parts_rsvp_response_count(string $response)
    {
        $parts = VoicePart::all();
        foreach($parts as $part)
        {
            $part->response_count = $part->singers()->whereHas('rsvps', function(Builder $query) use ($response) {
                $query->where('event_id', '=', $this->id)
                    ->where('response', '=', $response);
            })->count();
        }
        return $parts;
    }

    public function singers_rsvp_missing(): Builder
    {
        return Singer::whereDoesntHave('rsvps', function(Builder $query) {
            $query->where('event_id', '=', $this->id);
        });
    }

    public function singers_attendance(string $response): Builder
    {
        return Singer::whereHas('attendances', function(Builder $query) use ($response) {
            $query->where('event_id', '=', $this->id)
                ->where('response', '=', $response);
        });
    }
    public function singers_attendance_missing(): Builder
    {
        return Singer::whereDoesntHave('attendances', function(Builder $query) {
            $query->where('event_id', '=', $this->id);
        });
    }
    public function voice_parts_attendance_count(string $response)
    {
        $parts = VoicePart::all();
        foreach($parts as $part)
        {
            $part->response_count = $part->singers()->whereHas('attendances', function(Builder $query) use ($response) {
                $query->where('event_id', '=', $this->id)
                    ->where('response', '=', $response);
            })->count();
        }
        return $parts;
    }


    public function getStartDateAttribute(string $value): Carbon
    {
        return tz_from_utc_to_tenant($value);
    }
    public function setStartDateAttribute(string $value): void
    {
        $this->attributes['start_date'] = tz_from_tenant_to_utc($value);
    }

    public function getEndDateAttribute(string $value): Carbon
    {
        return tz_from_utc_to_tenant($value);
    }
    public function setEndDateAttribute(string $value): void
    {
        $this->attributes['end_date'] = tz_from_tenant_to_utc($value);
    }

    public function getCallTimeAttribute(string $value): Carbon
    {
        return tz_from_utc_to_tenant($value);
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
        return $this->start_date < Carbon::now();
    }

    public function getInFutureAttribute(): bool
    {
        return $this->start_date > Carbon::now();
    }

    public function getIsRepeatParentAttribute(): bool
    {
        return $this->repeat_parent_id === $this->id;
    }

    public function getAddToCalendarLinkAttribute(): Link
    {
        if(! isset($this->_add_to_calendar_link)) {
            $this->_add_to_calendar_link = Link::create($this->title, $this->call_time, $this->end_date)
                ->description($this->description ?? '')
                ->address($this->location_address ?? '');
        }
        return $this->_add_to_calendar_link;
    }
}
