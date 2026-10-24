<?php

namespace App\Services;

use App\Enums\EmailCategory;
use App\Enums\ProductAnnouncementAudience;
use App\Enums\SingerStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ProductAnnouncementRecipients
{
    public function query(ProductAnnouncementAudience $audience, EmailCategory $category): Builder
    {
        $query = User::query()->without(['media', 'membership.roles', 'membership.status']);

        if (! $audience->includesAllUsers()) {
            $roleName = $audience->membershipRole();

            $query->whereHas('memberships', function (Builder $membershipQuery) use ($roleName): void {
                $membershipQuery
                    ->withoutTenancy()
                    ->whereHas('status', fn (Builder $statusQuery) => $statusQuery
                        ->where('status', SingerStatus::MEMBERS->value));

                if ($roleName !== null) {
                    $membershipQuery->whereHas('roles', fn (Builder $roleQuery) => $roleQuery
                        ->withoutTenancy()
                        ->where('name', $roleName));
                }
            });
        }

        if (! $category->isMandatory()) {
            $query->where('email_preferences->' . $category->value, true);
        }

        return $query->orderBy('users.id');
    }
}
