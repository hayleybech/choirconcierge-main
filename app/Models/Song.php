<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Song
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $pitch_blown
 * @property int $status_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property int $tenant_id
 * @property bool $show_for_prospects
 *
 * Relationships
 * @property SongStatus $status
 * @property Collection<SongCategory> $categories
 * @property Collection<SongAttachment> $attachments
 * @property Collection<Membership> $members
 *
 * Dynamic
 * @property string $pitch
 * @property LearningStatus $my_learning
 */
class Song extends Model
{
    use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'pitch_blown', 'show_for_prospects', 'suppress_email', 'description'];

    protected $with = ['categories', 'status'];

    public const PITCHES = [
        0 => 'A',
        1 => 'A#/Bb',
        2 => 'B',
        3 => 'C',
        4 => 'C#/Db',
        5 => 'D',
        6 => 'D#/Eb',
        7 => 'E',
        8 => 'F',
        9 => 'F#/Gb',
        10 => 'G',
        11 => 'G#/Ab',
    ];

    public const KEYS = [
        'Major' => self::PITCHES,
        'Minor' => self::PITCHES,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['pitch'];

    protected static function booted(): void
    {
        // @todo convert this to a regular scope to prevent unwanted side effects
        static::addGlobalScope('filterPending', static function (Builder $builder): void {
            $builder->unless(Auth::user()?->isSuperAdmin || Auth::user()?->membership?->hasAbility('songs_update'), function (Builder $query): Builder {
                return $query->whereDoesntHave('status', static function (Builder $query): Builder {
                    return $query->where('title', '=', 'Pending');
                });
            });
        });
    }

    public static function create(array $attributes = [])
    {
        $status = SongStatus::find($attributes['status']);
        $categories = $attributes['categories'] ?? [];

        unset($attributes['status'], $attributes['categories']);

        /** @var Song $song */
        $song = static::query()->create($attributes);

        // Associate status
        $status->songs()->save($song);

        // Attach categories
        $song->categories()->attach($categories);
        $song->save();

        return $song;
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Associate status
        $status = SongStatus::find($attributes['status']);
        $status->songs()->save($this);

        // Attach categories
        $this->categories()->sync($attributes['categories']);
        $this->save();

        return true;
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SongStatus::class, 'status_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(SongCategory::class, 'songs_song_categories', 'song_id', 'category_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SongAttachment::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'membership_song', 'song_id', 'membership_id')
            ->withPivot(['status'])
            ->using(LearningStatus::class)
            ->as('learning')
            ->withTimestamps();
    }

    public function getMyLearningAttribute(): LearningStatus
    {
        if(! auth()->user()->membership) {
            return LearningStatus::getNullLearningStatus();
        }

        return $this->members()
            ->where('memberships.id', '=', auth()->user()->membership->id)
            ->first()
            ?->learning
            ?? LearningStatus::getNullLearningStatus();
    }

    public function getPitchAttribute(): string
    {
        return self::getAllPitches()[$this->pitch_blown];
    }

    public static function getAllPitches(): array
    {
        $all_pitches = [];
        foreach (self::KEYS as $mode => $pitches) {
            $all_pitches = array_merge($all_pitches, $pitches);
        }

        return $all_pitches;
    }

    public function createMissingLearningRecords(): void
    {
        $this->members()->attach(
            Membership::whereDoesntHave('songs', function ($query) {
                $query->where('songs.id', $this->id);
            })->pluck('id'),
            ['status' => 'not-started']
        );
    }
}
