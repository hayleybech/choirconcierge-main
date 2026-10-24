<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
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
                ->where('navigation.1.name', 'Communications')
                ->where('navigation.1.route', 'communications.index')
                ->where('navigation.1.items.0.name', 'Send Broadcast')
            );
    }

    public function test_navigation_uses_inbox_labels_when_the_tenant_feature_is_enabled(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));
        Feature::for(tenancy()->tenant)->activate('inbox');

        $this->get(the_tenant_route('dash'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('navigation.1.name', 'Inbox')
                ->where('navigation.1.items.0.name', 'Compose')
            );
    }

    public function test_app_stylesheet_uses_the_versioned_mix_asset(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('dash'))
            ->assertSee('href="'.e(mix('/css/app.css')).'"', false);
    }
}
