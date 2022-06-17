<?php

namespace App\Jobs;

use App\Mail\CloneMessage;
use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailForGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Mailable $message;

    public UserGroup $group;

    public function __construct(Mailable $message, UserGroup $group)
    {
        $this->message = $message;

        $this->group = $group->withoutRelations();
    }

    public function handle()
    {
        CloneMessage::forGroup($this->message, $this->group);
    }
}
