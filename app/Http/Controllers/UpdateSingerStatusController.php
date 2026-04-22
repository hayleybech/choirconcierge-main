<?php

namespace App\Http\Controllers;

use App\Enums\SingerStatus;
use App\Models\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateSingerStatusController extends Controller
{
    public function __invoke(Membership $singer, Request $request): RedirectResponse
    {
        $this->authorize('create', $singer);

        $request->validate(['move_status' => ['required', Rule::enum(SingerStatus::class)]]);

        $status = $request->input('move_status');

        $singer->statuses()->create(['status' => $status]);

        return redirect()
            ->back()
            ->with(['status' => 'The singer was moved. ']);
    }
}
