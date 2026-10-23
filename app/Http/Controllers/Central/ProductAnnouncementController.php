<?php

namespace App\Http\Controllers\Central;

use App\Enums\EmailCategory;
use App\Enums\ProductAnnouncementAudience;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAnnouncementRequest;
use App\Jobs\QueueProductAnnouncement;
use App\Models\MailLog;
use App\Services\ProductAnnouncementRecipients;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductAnnouncementController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Central/Announcements/Create', [
            'audiences' => collect(ProductAnnouncementAudience::cases())->map(fn (ProductAnnouncementAudience $audience) => [
                'key' => $audience->value,
                'label' => $audience->label(),
            ]),
            'categories' => collect(EmailCategory::productCategories())->map(fn (EmailCategory $category) => [
                'key' => $category->value,
                'label' => $category->label(),
            ]),
        ]);
    }

    public function store(ProductAnnouncementRequest $request, ProductAnnouncementRecipients $recipients): RedirectResponse
    {
        $data = $request->validated();
        $audience = ProductAnnouncementAudience::from($data['audience']);
        $category = EmailCategory::from($data['category']);
        $recipientCount = $recipients->query($audience, $category)->count();

        if ($recipientCount === 0) {
            return back()->withErrors([
                'audience' => 'No recipients match this audience and email preference.',
            ]);
        }

        $fileMeta = collect($request->file('attachments', []))
            ->map(function (UploadedFile $file): array {
                Storage::disk('temp')->putFile('broadcasts', $file);

                return [
                    'hashName' => $file->hashName(),
                    'originalName' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            })
            ->values()
            ->all();

        $mailLog = MailLog::create([
            'uid' => 'product-announcement-' . Str::uuid(),
            'from' => 'Choir Concierge <hayley@choirconcierge.com>',
            'to' => $audience->label(),
            'subject' => Str::limit($data['subject'], 125),
            'body' => $data['body'],
            'size' => collect($fileMeta)->sum('size') + strlen($data['body']),
            'has_attachments' => $fileMeta !== [],
            'received_at' => now(),
        ]);

        $mailLog->events()->create([
            'status' => 'pending',
            'context' => $recipientCount . ' recipients · ' . $category->label(),
        ]);

        QueueProductAnnouncement::dispatch(
            $audience->value,
            $category->value,
            $data['subject'],
            $data['body'],
            $mailLog->id,
            $fileMeta,
        );

        return redirect()
            ->route('central.mail-logs.index')
            ->with(['status' => 'Email queued for ' . $recipientCount . ' recipients.']);
    }
}
