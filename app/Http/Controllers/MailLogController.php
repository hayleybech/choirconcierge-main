<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use App\Models\UserGroup;
use Inertia\Inertia;
use Inertia\Response;

class MailLogController extends Controller
{
    public function index(): Response
    {
        // @todo create a mail log policy
        $this->authorize('createBroadcast', UserGroup::class);

        // @todo filter by lists the user is a member of (or admin)
        return Inertia::render('MailingLists/MailLogs/Index', [
            'logs' => MailLog::query()
                ->where('to', 'like', '%@'.tenant('primary_domain').'%')
                ->orWhere('cc', 'like', '%@'.tenant('primary_domain').'%')
                ->orWhere('bcc', 'like', '%@'.tenant('primary_domain').'%')
                ->with('latestEvent')
                ->paginate(50),
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        $this->authorize('createBroadcast', UserGroup::class);

        // @todo auth by lists the user is a member of

        return Inertia::render('MailingLists/MailLogs/Show', [
            'log' => $mail_log->load('events'),
        ]);
    }
}
