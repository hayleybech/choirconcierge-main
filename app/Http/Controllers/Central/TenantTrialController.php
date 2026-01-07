<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TenantTrialController extends Controller
{
    public function update(Tenant $tenant){
        $tenant->customer->trial_ends_at = Carbon::now()->addDays(30);
        $tenant->customer->save();
        $tenant->save();

        return redirect()->back()->with(['status' => 'Trial reset. ']);
    }
}
