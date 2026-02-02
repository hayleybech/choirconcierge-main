<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\MailLog;
use Inertia\Inertia;
use Inertia\Response;

class MailLogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Central/MailLogs/Index', [
            'logs' => MailLog::with('latestEvent')
                ->latest()
                ->paginate(50),
        ]);
    }

    public function show(MailLog $mail_log): Response
    {
        return Inertia::render('Central/MailLogs/Show', [
            'log' => $mail_log->load('events.user_group'),
        ]);
    }
}
