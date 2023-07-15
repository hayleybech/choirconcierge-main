<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ImportSingerController
 */
class ImportSingerControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function groupanizer_import_creates_users(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/groupanizer-singers.csv'),
            'groupanizer-singers.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs(
                $this->createUserWithRole('Admin')
            )
            ->post(the_tenant_route('singers.import'), [
                'import_csv' => [$file],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'first_name' => 'Jono',
            'last_name' => 'Albertini',
            'email' => 'jonoalbo7@gmail.com',
            'dob' => (new Carbon(new DateTime('1989-03-17')))->toDateTimeString(),
            'phone' => '(04) 0793-3305',
            'address_street_1' => '3 / 7 Blake St Southport',
            'address_street_2' => '',
            'address_suburb' => 'Southport',
            'address_state' => 'QLD',
            'address_postcode' => '4215',
            'skills' => '',
            'height' => null,
        ]);
    }

    /** @test */
    public function groupanizer_import_creates_singers_for_users(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/groupanizer-singers.csv'),
            'groupanizer-singers.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs(
            $this->createUserWithRole('Admin')
        )
            ->post(the_tenant_route('singers.import'), [
                'import_csv' => [$file],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('memberships', [
            'onboarding_enabled' => false,
            'membership_details' => 'BHA  1945 Blenders old No 245',
            'joined_at' => '2007-01-31 00:00:00',
            'singer_category_id' => SingerCategory::firstWhere('name', 'Members')->id,
            'voice_part_id' => VoicePart::firstWhere('title', 'Lead')->id,
            'user_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->id,
        ]);
    }

    /** @test */
    public function groupanizer_import_assigns_roles_to_singers(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/groupanizer-singers.csv'),
            'groupanizer-singers.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs(
            $this->createUserWithRole('Admin')
        )
            ->post(the_tenant_route('singers.import'), [
                'import_csv' => [$file],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->membership->id,
            'role_id' => Role::firstWhere('name', 'Admin')->id,
        ]);
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->membership->id,
            'role_id' => Role::firstWhere('name', 'Music Team')->id,
        ]);
    }

    /** @test */
    public function groupanizer_import_updates_existing_users(): void
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function harmonysite_import_creates_users(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/harmonysite-singers.csv'),
            'harmonysite-singers.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs(
            $this->createUserWithRole('Admin')
        )
            ->post(the_tenant_route('singers.import'), [
                'import_csv' => [$file],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'first_name' => 'Nick',
            'last_name' => 'Schurmann',
            'email' => 'nick.s@internode.on.net',
            'dob' => '1991-07-08',
            'phone' => '0432 837 215',
            'address_street_1' => '8 Scaddan Street',
            'address_street_2' => '',
            'address_suburb' => 'Wembley',
            'address_state' => 'WA',
            'address_postcode' => '6014',
            'height' => null,
            'ice_name' => '',
            'profession' => '',
        ]);
    }

    /** @test */
    public function harmonysite_import_creates_singers_for_users(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/harmonysite-singers.csv'),
            'groupanizer-singers.csv',
            'text/csv',
            null,
            true
        );

        $this->actingAs(
            $this->createUserWithRole('Admin')
        )
            ->post(the_tenant_route('singers.import'), [
                'import_csv' => [$file],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('memberships', [
            'user_id' => User::firstWhere('email', 'nick.s@internode.on.net')->id,
            'onboarding_enabled' => false,
            'joined_at' => '2015-07-17 00:00:00',
            'singer_category_id' => SingerCategory::firstWhere('name', 'Members')->id,
            'voice_part_id' => VoicePart::firstWhere('title', 'Bass')->id,
        ]);
    }

    /** @test */
    public function harmonysite_import_assigns_roles_to_singers(): void
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function harmonysite_import_updates_existing_users(): void
    {
        $this->markTestIncomplete();
    }
}
