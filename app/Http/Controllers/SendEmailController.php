<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailForGroup;
use App\Mail\ChoirBroadcast;
use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SendEmailController extends Controller
{
    public function create(Request $request): InertiaResponse
    {
//        $this->authorize('send', Song::class);

        // @todo discuss with vic - mailing list permissions vs send email permissions

        return Inertia::render('MailingLists/SendEmail', [
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

        $this->authorize('sendEmailTo', $group);

        SendEmailForGroup::dispatch(
            (new ChoirBroadcast())
                ->from($request->user()->email, auth()->user()->name)
                ->subject($request->input('subject'))
                ->html($request->input('body')),
            $group
        );

        return redirect()
            ->route('groups.index')
            ->with(['status' => 'Email sent! ']);
    }
}
