<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class DefaultDashController extends Controller
{
    public function index(): RedirectResponse
    {
        $userChoirs = $this->getUserChoirs();

        if(count($userChoirs) === 1) {
            return redirect()->route('dash', ['tenant' => $userChoirs[0]]);
        }

        $default_tenant = auth()->user()->default_tenant_id;
        if(count($userChoirs) > 1 && $default_tenant !== null) {
            return redirect()->route('dash', ['tenant' => $default_tenant]);
        }

        return redirect()->route('central.dash');
    }
    private function getUserChoirs(): Collection
    {
        return session()->has('impersonation:active')
            ? collect([tenant()->load('domains')])
            : auth()->user()
                ?->singers()
                ->withoutTenancy()
                ->with('tenant.domains')
                ->get()
                ->map(fn($singer) => $singer->tenant);
    }

    public function update(Tenant $default_dash): RedirectResponse
    {
        /* @var User $user */
        $user = auth()->user();

        $user->defaultTenant()->associate($default_dash);
        $user->save();

        return redirect()
            ->route('central.dash')
            ->with(['status' => 'Default choir updated. ']);
    }
}
