<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashController extends Controller
{
	public function index(): Response
	{
		return Inertia::render('Central/Dash/Show');
	}
}
