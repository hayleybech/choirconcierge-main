<?php

namespace App\Models;

use App\Models\Filters\Event_TypeFilter;
use App\Models\Filters\Filterable;
use App\Notifications\EventCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Event
 *
 * Columns
 * @property int id
 * @property string title
 * @property int type_id
 * @property Carbon call_time
 * @property Carbon start_date
 * @property Carbon end_date
 * @property string location_place_id
 * @property string location_icon
 * @property string location_name
 * @property string location_address
 * @property string description
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * Relationships
 * @property EventType type
 * @property Rsvp[] rsvps
 *
 * @package App
 */
class Event extends Model
{
    use Filterable, BelongsToTenant;

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
    ];

    protected static $filters = [
        Event_TypeFilter::class,
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
    ];

    public static function create( array $attributes = [] )
    {
        /** @var Event $event */
        $event = static::query()->create($attributes);

        $event->type = $attributes['type'];

        Notification::send(User::active()->get(), new EventCreated($event));

        return $event;
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        $this->type = $attributes['type'];
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

    public function my_rsvp(): Rsvp
    {
        return $this->rsvps()
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

    public function singers_rsvp_missing(): Builder
    {
        return Singer::whereDoesntHave('rsvps', function(Builder $query) {
            $query->where('event_id', '=', $this->id);
        });
    }

    public function isUpcoming(): bool
    {
        return $this->start_date->greaterThan(Carbon::now());
    }
}
