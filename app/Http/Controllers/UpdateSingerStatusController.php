<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateSingerStatusController extends Controller
{
    public function __invoke(Membership $singer, Request $request): RedirectResponse
    {
        $this->authorize('create', $singer);

        $request->validate(['move_status' => 'required|numeric|gt:0']);

        $statusId = $request->input('move_status', 0);

        // Attach to Prospects status
        $singer->statuses()->attach($statusId);

        return redirect()
            ->back()
            ->with(['status' => 'The singer was moved. ']);
    }
}
