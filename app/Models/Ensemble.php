<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Columns
 * @property int $id
 * @property string $name
 * @property string $logo
 * @property string $tenant_id
 *
 * Attributes
 * @property string $logo_url
 *
 * Relationships
 * @property Tenant $organisation
 * @property Collection<Enrolment> $enrolments
 * @property Collection<Membership> $members
 * @property Collection<User> $users
 * @property Collection<Song> $songs
 * @property Collection<Event> $events
 * @property Collection<RiserStack> $riserStacks
 */

class Ensemble extends Model
{
    use HasFactory, BelongsToTenant;

	protected $guarded = [];

	protected $appends = ['logo_url'];

	public function organisation(): BelongsTo {
		return $this->belongsTo(Tenant::class);
	}

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'ensemble_song', 'ensemble_id', 'song_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'ensemble_event', 'ensemble_id', 'event_id');
    }

    public function riserStacks(): BelongsToMany
    {
        return $this->belongsToMany(RiserStack::class, 'ensemble_riser_stack', 'ensemble_id', 'riser_stack_id');
    }

	public function logoUrl(): Attribute
	{
		return Attribute::get(fn () =>
			$this->logo ? Storage::disk('public')->url('choir-logos/'.$this->logo) : ''
		);
	}

	/**
	 * @throws Exception
	 */
	public function updateLogo(UploadedFile|string $logo, string $hash_name)
	{
		if (!Storage::disk('public')->exists('choir-logos')) {
			Storage::disk('public')->makeDirectory('choir-logos');
		}
		if (!Storage::disk('public')
			->putFileAs('choir-logos', $logo, $hash_name)
		) {
			throw new Exception('Failed to save the logo.');
		}

		$this->update(['logo' => $hash_name]);
	}
}
