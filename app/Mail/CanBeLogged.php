<?php

namespace App\Mail;

use Carbon\Carbon;

trait CanBeLogged
{
    public string $uid; // UID from the IMAP mailbox, used for logging

    public bool $has_attachments;

    public Carbon $received_at;
    public int $size = 0;

    public function getUid(): string {
        return $this->uid;
    }

    public function getHasAttachments(): bool
    {
        return $this->has_attachments;
    }

    public function getReceivedAt(): Carbon {
        return $this->received_at;
    }

    public function getSize(): int {
        return $this->size;
    }
}