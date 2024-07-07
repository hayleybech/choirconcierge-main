<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrackTenantSalesDemoController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Tenant $tenant): RedirectResponse
    {
		$this->authorize('viewAny', Tenant::class);

        $tenant->update([
			'had_demo' => true,
        ]);

	    return redirect()->back()->with(['status' => 'Sales demo status saved.']);
    }
}
