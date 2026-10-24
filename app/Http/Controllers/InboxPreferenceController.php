<?php

namespace App\Http\Controllers;

use App\Features\InboxFeature;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InboxPreferenceController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isSuperAdmin, 403);

        $preference = $request->validate([
            'preference' => ['required', Rule::in(['inherit', 'enabled', 'disabled'])],
        ])['preference'];

        InboxFeature::setPersonalOverride($user, match ($preference) {
            'enabled' => true,
            'disabled' => false,
            'inherit' => null,
        });

        $redirect = $request->routeIs('central.account.inbox-preference.update')
            ? redirect()->route('central.account.edit', ['tab' => 'inbox'])
            : redirect()->back();

        return $redirect->with('status', 'Inbox preview preference updated.');
    }
}
