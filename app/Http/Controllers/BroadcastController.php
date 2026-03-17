<?php

namespace App\Http\Controllers;

use App\Http\Requests\BroadcastRequest;
use App\Jobs\SendEmailForGroup;
use App\Mail\OrganisationBroadcast;
use App\Models\MailLog;
use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Storage;

class BroadcastController extends Controller
{
    public function create(Request $request): InertiaResponse
    {
        $this->authorize('createBroadcast', UserGroup::class);

        return Inertia::render('MailingLists/Broadcasts/Create', [
            'lists' => UserGroup::with(['tenant', 'sender_roles', 'recipient_roles', 'sender_ensembles', 'recipient_ensembles'])
                ->get()
                ->filter(fn(UserGroup $group) => $group->authoriseSender($request->user()))
                ->values(),
        ]);
    }

    public function store(BroadcastRequest $request): RedirectResponse
    {
        $group = UserGroup::find($request->input('list'));

        $this->authorize('createBroadcastFor', $group);

        $fileMeta = collect($request->file('attachments'))
            ->each(fn (UploadedFile $file) => Storage::disk('temp')->putFile('broadcasts', $file))
            ->map(fn (UploadedFile $file) => [
                'hashName' => $file->hashName(),
                'originalName' => $file->getClientOriginalName(),
            ]);

        $organisationBroadcast = new OrganisationBroadcast(
            $group,
            $request->input('subject'),
            $request->input('body'),
            $request->user(),
            $fileMeta,
            'broadcast-' . Str::uuid(),
        );

        $mailLog = MailLog::createFromMessage($organisationBroadcast);

        $mailLog->events()->createMany([
            [
                'status' => 'pending',
            ],
            [
                'status' => 'group-found',
                'context' => $group->title,
                'user_group_id' => $group->id,
            ],
        ]);

        SendEmailForGroup::dispatch($organisationBroadcast, $group);

        return redirect()
            ->route('groups.mail-logs.index')
            ->with(['status' => 'Email sent! ']);
    }
}
