<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Vite;
use Laravel\Pennant\Feature;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    public function test_navigation_data_is_shared_via_inertia(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('dash'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('navigation')
                ->where('navigation.0.name', 'Dashboard')
                ->where('navigation.0.route', 'dash')
                ->where('navigation.6.name', 'Communications')
                ->where('navigation.6.route', 'communications.index')
                ->where('navigation.6.items.0.name', 'Send Broadcast')
            );
    }

    public function test_navigation_uses_inbox_labels_when_the_tenant_feature_is_enabled(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
        Feature::for(tenancy()->tenant)->activate('inbox');

        $this->get(the_tenant_route('dash'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('navigation.6.name', 'Inbox')
                ->where('navigation.6.items.0.name', 'Compose')
            );
    }
}
