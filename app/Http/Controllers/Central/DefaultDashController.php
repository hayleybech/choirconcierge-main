<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DefaultDashController extends Controller
{
    public function index(): RedirectResponse
    {
        $userChoirs = $this->getUserChoirs();

        return count($userChoirs) > 1
            ? redirect()->route('central.dash')
            : redirect()->route('dash', ['tenant' => $userChoirs[0]]);
    }
    private function getUserChoirs()
    {
        return session()->has('impersonation:active')
            ? [tenant()->load('domains')]
            : auth()->user()
                ?->singers()
                ->withoutTenancy()
                ->with('tenant.domains')
                ->get()
                ->map(fn($singer) => $singer->tenant);
    }
}
