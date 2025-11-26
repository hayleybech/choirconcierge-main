<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CustomField extends Model
{
    use HasFactory, BelongsToTenant;

    public $guarded = [];

    public function memberships(): BelongsToMany
    {
        return $this->belongsToMany(Membership::class, 'custom_field_entries')
            ->as('entry')
            ->using(CustomFieldEntry::class)
            ->withTimestamps();
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CustomFieldEntry::class, 'custom_field_id', 'id', '');
    }
}
