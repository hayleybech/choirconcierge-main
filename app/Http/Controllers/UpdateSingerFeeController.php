<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UpdateSingerFeeController extends Controller
{
    public function __invoke(Request $request, Membership $singer): RedirectResponse
    {
        Gate::authorize('update-fees');

        $request->validate([
            'paid_until' => ['nullable', 'sometimes', 'date'],
        ]);

        $singer->update(['paid_until' => $request->input('paid_until')]);

        return redirect()
            ->back()
            ->with(['status' => 'Membership fees updated. ']);
    }
}
