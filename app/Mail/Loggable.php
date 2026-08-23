<?php

namespace App\Mail;

use Carbon\Carbon;

interface Loggable
{
    public function getUid(): string; // UID from the IMAP mailbox, used for logging

    public function getHasAttachments(): bool;

    public function getReceivedAt(): Carbon;

    public function getContent(): string;
    public function getSize(): int;
}