<?php

namespace App\Models;

use App\Models\Filters\Event_DateFilter;
use App\Models\Filters\Event_TypeFilter;
use App\Models\Filters\Filterable;
use App\Notifications\EventCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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

    public static function create( array $attributes = [], bool $send_notifications = true )
    {
        /** @var Event $event */
        $event = static::query()->create($attributes);

        $event->type = $attributes['type'];

        if( $send_notifications ){
            Notification::send(User::active()->get(), new EventCreated($event));
        }

        $event->createRepeats();

        return $event;
    }

    /**
     * Generates occurrences for a repeating event
     * - Starts at the date of the second occurrence
     * - Increments the date by the frequency
     * - Loops until date is after the repeat end date
     * - Doesn't yet support repeat_frequency_amount (always = 1 for now)
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
        parent::update($attributes, $options);

        $this->type = $attributes['type'];

        $this->updateRepeats();

        return true;
    }

    private function updateRepeats(): void
    {
        if ( ! $this->is_repeating) {
            return;
        }

        $edit_mode = 'single';
        // Method 1 - Update ONLY this event (remove it from the series) e.g. Google Calender "Update only this event"
        //		- doing this would remove the parent_recurring_event_id from the single event and update its fields
        if($edit_mode === 'single') {
            $this->updateSingle();
        }
    }
    private function updateSingle(): void {
        // If this event was the parent, reset parent id on children to next child
        if($this->isRepeatParent()){
            $new_parent = $this->repeat_children()->orderBy('start_date')->first(); // second item
            optional($new_parent)->repeat_children()->saveMany($this->repeat_children);
        }
        // Reset parent id on this event
        $this->repeat_parent_id = 0;
        // Convert to single
        $this->is_repeating = false;
    }

    public function setTypeAttribute($typeId) {
        $type = EventType::find($typeId);
        $type->events()->save($this);
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

    public function isUpcoming(): bool
    {
        return $this->start_date->greaterThan(Carbon::now());
    }

    public function isRepeatParent(): bool
    {
        return $this->repeat_parent_id === $this->id;
    }
}
