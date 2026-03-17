<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use Symfony\Component\HttpFoundation\Response;

class MailLogOpenController extends Controller
{
    /**
     * Record a mail log open event.
     *
     * @param string $mailLogUid
     * @param string $email
     * @return Response
     */
    public function show(string $mailLogUid, string $email): Response
    {
        $mailLog = MailLog::where('uid', $mailLogUid)->first();

        if ($mailLog) {
            try {
                $mailLog->events()->create([
                    'status' => 'opened',
                    'context' => decrypt($email),
                ]);
            } catch (\Throwable $e) {
                // Ignore decryption errors
            }
        }

        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel, 200)
            ->header('Content-Type', 'image/gif');
    }
}
