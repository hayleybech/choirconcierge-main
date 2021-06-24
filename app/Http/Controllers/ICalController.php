<?php
namespace App\Http\Controllers;

use App\EventIcalFeed;
use Illuminate\Http\Response;

class ICalController extends Controller
{
	public function index(): Response
	{
		return response((new EventIcalFeed())->get())
			->header('Content-Type', 'text/calendar')
			->header('Content-Disposition', 'attachment; filename="events-calendar.ics"')
			->header('charset', 'utf-8');
	}
}
