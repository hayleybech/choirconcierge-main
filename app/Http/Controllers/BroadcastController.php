<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailForGroup;
use App\Mail\ChoirBroadcast;
use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BroadcastController extends Controller
{
    public function create(Request $request): InertiaResponse
    {
        $this->authorize('createBroadcast', UserGroup::class);

        return Inertia::render('MailingLists/Broadcasts/Create', [
            'lists' => UserGroup::with(['tenant', 'sender_roles', 'recipient_roles'])
                ->get()
                ->filter(fn(UserGroup $group) => $group->authoriseSender($request->user()))
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'list' => ['required', 'exists:user_groups,id'],
            'subject' => ['required', 'max:255'],
            'body' => ['required', 'max:5000'],
        ]);

        $group = UserGroup::find($request->input('list'));

        $this->authorize('createBroadcastFor', $group);

        SendEmailForGroup::dispatch(
            (new ChoirBroadcast(
                $request->input('subject'),
                $request->input('body'),
                $request->user(),
            )),
            $group
        );

        return redirect()
            ->route('groups.index')
            ->with(['status' => 'Email sent! ']);
    }
}
