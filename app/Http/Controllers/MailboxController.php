<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessGroupMailbox;

class MailboxController extends Controller
{
	public function process(ProcessGroupMailbox $processGroupMailbox): void
	{
		$processGroupMailbox->handle();
	}
}
