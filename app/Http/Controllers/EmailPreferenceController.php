<?php

namespace App\Http\Controllers;

use App\Enums\EmailCategory;
use App\Http\Requests\UpdateEmailPreferencesRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class EmailPreferenceController extends Controller
{
    public function update(UpdateEmailPreferencesRequest $request): RedirectResponse
    {
        $user = $request->user();
        $preferences = $request->validated('email_preferences');

        if (! $user->canReceiveAttendanceReports()) {
            unset($preferences[EmailCategory::ATTENDANCE_REPORTS->value]);
        }

        $user->updateEmailPreferences($preferences);

        return redirect()
            ->back()
            ->with(['status' => 'Email notification preferences updated.']);
    }

    public function unsubscribe(User $user, string $category): RedirectResponse
    {
        $emailCategory = EmailCategory::tryFrom($category);

        abort_if($emailCategory === null || $emailCategory->isMandatory(), 404);

        $preferences = $user->emailPreferences();
        $preferences[$emailCategory->value] = false;
        $user->updateEmailPreferences($preferences);

        return redirect()
            ->route('login')
            ->with(['status' => 'You have been unsubscribed from ' . $emailCategory->label() . ' emails.']);
    }
}
