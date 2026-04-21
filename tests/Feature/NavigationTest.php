<?php

namespace Tests\Feature;

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
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
            );
    }
}
