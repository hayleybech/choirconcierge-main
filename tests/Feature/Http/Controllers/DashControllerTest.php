<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DashController
 */
class DashControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team')); // Any role is fine

        $this->get(the_tenant_route('dash'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dash/Show')
                ->has('events')
                ->has('songs')
                ->has('birthdays')
                ->has('emptyDobs')
                ->has('memberversaries')
            );
    }
}
