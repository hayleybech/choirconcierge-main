<?php

namespace App\Navigation;

use App\Models\Tenant;

class UserNavigation
{
    public function get(?string $tenant, bool $forApi = false): array
    {
        $items = $tenant && Tenant::find($tenant)
            ? $this->tenantItems($forApi)
            : $this->centralItems($forApi);

        $items = array_values(array_filter($items));

        if ($forApi) {
            $items = array_values(array_filter($items, fn($item) => !isset($item['action']) && (!isset($item['hide']) || $item['hide'] === false)));

            return array_map(fn(array $item): array => $this->transformForApi($item, $tenant), $items);
        }

        return $items;
    }

    protected function tenantItems(bool $forApi): array
    {
        $user = auth()->user();
        $membership = $user?->membership;
        $impersonationActive = session()->has('impersonation:active');
        $canImpersonate = $user?->isSuperAdmin || $user?->membership?->hasRole('Admin');
        $canUpdateTenant = $user?->can('update', [Tenant::class, null]);

        return [
            $membership ? [
                'name' => 'Your Profile',
                'route' => 'singers.show',
                'params' => ['singer' => $membership->id],
                'icon' => 'user',
            ] : null,
            [
                'name' => 'Edit Profile',
                'route' => 'account.edit',
                'icon' => 'user-edit',
            ],
            [
                'name' => 'Impersonate User',
                'action' => 'impersonate_modal',
                'icon' => 'user-unlock',
                'hide' => !$canImpersonate || $impersonationActive || $forApi,
            ],
            [
                'name' => 'Stop Impersonating',
                'route' => 'impersonation.stop',
                'icon' => 'user-lock',
                'hide' => $canImpersonate || !$impersonationActive || $forApi,
            ],
            [
                'name' => 'Organisation Settings',
                'route' => 'organisation.edit',
                'icon' => 'cogs',
                'hide' => !$canUpdateTenant || $forApi,
            ],
            [
                'name' => 'Changelog',
                'route' => 'central.changelog',
                'icon' => 'code-merge',
                // @todo add back in once support for central is added to app?
                'hide' => $forApi,
            ],
            [
                'name' => 'Help (Email Us)',
                'route' => 'mailto:hayley@choirconcierge.com',
                'icon' => 'question',
                'external' => true,
                'hide' => $forApi,
            ],
            [
                'name' => 'Sign out',
                'route' => 'logout',
                'method' => 'POST',
                'icon' => 'sign-out-alt',
                'hide' => $forApi,
            ],
        ];
    }

    protected function centralItems(bool $forApi): array
    {
        return [
            [
                'name' => 'Edit Profile',
                'route' => 'central.account.edit',
                'icon' => 'user-edit',
            ],
            [
                'name' => 'Changelog',
                'route' => 'central.changelog',
                'icon' => 'code-merge',
            ],
            [
                'name' => 'Help (Email Us)',
                'route' => 'mailto:hayley@choirconcierge.com',
                'icon' => 'question',
                'external' => true,
                'hide' => $forApi,
            ],
            [
                'name' => 'Sign out',
                'route' => 'logout',
                'method' => 'POST',
                'icon' => 'sign-out-alt',
                'hide' => $forApi,
            ],
        ];
    }

    protected function transformForApi(array $item, string $tenant): array
    {
        if (isset($item['route'])) {
            $item['url'] = $this->generateUrl(
                $item['route'],
                $tenant ? ['tenant' => $tenant, ...$item['params'] ?? []] : $item['params'] ?? []
            );
            unset($item['params']);
        }

        return $item;
    }

    protected function generateUrl(string $route, array $params = []): string
    {
        if (str_starts_with($route, 'mailto:')) {
            return $route;
        }

        try {
            return route($route, $params, false);
        } catch (\Exception $e) {
            return $route;
        }
    }
}
