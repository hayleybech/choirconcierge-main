<?php

namespace App\Services;

use App\Enums\EmailCategory;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EmailPreferenceLinks
{
    /**
     * @return array{manage: string, unsubscribe: string, category: string}
     */
    public function make(User $user, EmailCategory $category): array
    {
        return [
            'manage' => route('central.account.edit', ['tab' => 'notifications']),
            'unsubscribe' => URL::signedRoute('email-preferences.unsubscribe', [
                'user' => $user->getKey(),
                'category' => $category->value,
            ]),
            'category' => $category->label(),
        ];
    }
}
