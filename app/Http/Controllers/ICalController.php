<?php
namespace App\Http\Controllers;

use App\EventIcalFeed;
use Illuminate\Http\Response;

class ICalController extends Controller
{
    public function index(): Response {
        return response( (new EventIcalFeed())->get() )
            ->header('Content-Type', 'text/calendar')
            ->header('charset', 'utf-8');
    }
}