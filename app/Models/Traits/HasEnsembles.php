<?php

namespace App\Models\Traits;

use App\Models\Ensemble;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

trait HasEnsembles
{
    public function ensembles(): BelongsToMany
    {
        $singularTable = Str::singular($this->getTable());

        return $this->belongsToMany(
            Ensemble::class,
            "ensemble_{$singularTable}",
            $this->getForeignKey(),
            'ensemble_id',
        );
    }

    public function scopeForEnsembles(Builder $query, ?User $user = null): Builder
    {
        if (Ensemble::count() <= 1) {
            return $query;
        }

        $user ??= auth()->user();
        $membership = $user?->membership;

        if (! $membership || Gate::forUser($user)->allows('update', $query->getModel())) {
            return $query;
        }

        $ensembleIds = $membership->enrolments()->pluck('ensemble_id');

        return $query->where(function (Builder $query) use ($ensembleIds): void {
            $query->whereDoesntHave('ensembles')
                ->orWhereHas('ensembles', function (Builder $query) use ($ensembleIds): void {
                    $query->whereKey($ensembleIds);
                });
        });
    }
}
