<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CentralDashController extends Controller
{
	public function index(): Response
	{
		return Inertia::render('Central/Dash/Show');
	}
}
