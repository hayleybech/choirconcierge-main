<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Task;
use App\Models\Singer;

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
