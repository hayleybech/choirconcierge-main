<?php

namespace App\Services;

use App\Enums\EmailCategory;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class EmailPreferenceLinks
{
    /**
     * @return array{manage: string, category: string, context: string, unsubscribe?: string}
     */
    public function make(User $user, EmailCategory $category): array
    {
        $links = [
            'manage' => route('central.account.edit', ['tab' => 'notifications']),
            'category' => $category->label(),
            'context' => $category->preferenceContext(),
        ];

        if (! $category->isMandatory()) {
            $links['unsubscribe'] = URL::signedRoute('email-preferences.unsubscribe', [
                'user' => $user->getKey(),
                'category' => $category->value,
            ]);
        }

        return $links;
    }
}
