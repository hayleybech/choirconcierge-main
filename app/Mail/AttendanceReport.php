<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\VoicePart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceReport extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Event $event)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->event->tenant->mail_from_address, $this->event->tenant->mail_from_name),
            subject: 'Attendance Report: ' . $this->event->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $absent_singers = $this->event->relevant_memberships()
            ->whereHas('attendances', function (Builder $query) {
                $query->where('event_id', $this->event->id)
                    ->whereIn('response', ['absent', 'absent_apology', 'late_deemed_absent']);
            })
            ->with(['user', 'enrolments.voice_part', 'enrolments.ensemble'])
            ->get();

        $ensembleIds = $this->event->ensembles->pluck('id');
        $grouped = collect();

        foreach ($absent_singers as $membership) {
            $relevantEnrolments = $membership->enrolments->filter(function ($enrolment) use ($ensembleIds) {
                return $ensembleIds->contains($enrolment->ensemble_id);
            });

            if ($relevantEnrolments->isEmpty()) {
                $ensembleName = 'No Ensemble';
                // Try to find ANY voice part from their enrolments, or default to 'No Part'
                $voicePart = $membership->enrolments->firstWhere('voice_part_id', '!==', null)?->voice_part 
                    ?? $membership->enrolments->first()?->voice_part 
                    ?? VoicePart::getNullVoicePart();

                if (! $grouped->has($ensembleName)) {
                    $grouped->put($ensembleName, collect());
                }

                if (! $grouped->get($ensembleName)->has($voicePart->title)) {
                    $grouped->get($ensembleName)->put($voicePart->title, [
                        'voice_part' => $voicePart,
                        'singers' => collect(),
                    ]);
                }

                $grouped->get($ensembleName)->get($voicePart->title)['singers']->push($membership);
                continue;
            }

            foreach ($relevantEnrolments as $enrolment) {
                $ensembleName = $enrolment->ensemble->name;
                $voicePart = $enrolment->voice_part ?? VoicePart::getNullVoicePart();

                if (! $grouped->has($ensembleName)) {
                    $grouped->put($ensembleName, collect());
                }

                if (! $grouped->get($ensembleName)->has($voicePart->title)) {
                    $grouped->get($ensembleName)->put($voicePart->title, [
                        'voice_part' => $voicePart,
                        'singers' => collect(),
                    ]);
                }

                $grouped->get($ensembleName)->get($voicePart->title)['singers']->push($membership);
            }
        }

        // Sort ensembles and voice parts
        $grouped = $grouped->sortKeys()->map(function ($voiceParts) {
            return $voiceParts->sortKeys(SORT_NATURAL | SORT_FLAG_CASE);
        });

        return new Content(
            markdown: 'emails.attendance-report',
            with: [
                'url' => route('events.attendances.index', ['event' => $this->event, 'tenant' => $this->event->tenant->id]),
                'absent_singers' => $grouped,
                'show_ensembles' => $this->event->ensembles->count() > 1,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
