<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
 */

class Ensemble extends Model
{
    use HasFactory;

	protected $guarded = [];

	protected $appends = ['logo_url'];

	public function organisation(): BelongsTo {
		return $this->belongsTo(Tenant::class);
	}

	public function logoUrl(): Attribute
	{
		return Attribute::get(fn () =>
			$this->logo ? asset('storage/choir-logos/'.$this->logo) : ''
		);
	}

	/**
	 * @throws Exception
	 */
	public function updateLogo(UploadedFile|string $logo, string $hash_name)
	{
		if (!Storage::disk('global-public')->exists('choir-logos')) {
			Storage::disk('global-public')->makeDirectory('choir-logos');
		}
		if (!Storage::disk('global-public')
			->putFileAs('choir-logos', $logo, $hash_name)
		) {
			throw new Exception('Failed to save the logo.');
		}

		$this->update(['logo' => $hash_name]);
	}
}
