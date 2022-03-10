<?php

namespace App\Events;

use App\Models\Singer;
use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;

    public $singer;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Task $task
     * @param \App\Models\Singer $singer
     * @return void
     */
    public function __construct(Task $task, Singer $singer)
    {
        $this->task = $task;
        $this->singer = $singer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
