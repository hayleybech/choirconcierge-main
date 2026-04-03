<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Enums\SingerStatus;
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

    public function test_concierge_import_creates_users(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/concierge-singers.csv'),
            'concierge-singers.csv',
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

        // assert user created
        $this->assertDatabaseHas('users', [
            'first_name' => 'Dorcas',
            'last_name' => 'Weimann',
            'email' => 'hayes.ozella@example.com',
            'dob' => (new Carbon(new DateTime('1999-10-14')))->toDateTimeString(),
            'phone' => '+1 (838) 616-6120',
            'address_street_1' => '504 Brakus Bypass',
            'address_street_2' => 'Apt. 710',
            'address_suburb' => 'Casandraville',
            'address_state' => 'OK',
            'address_postcode' => '8000',
            'skills' => 'Rem tenetur explicabo et ut.',
            'height' => 39.64,
        ]);

        // assert membership created
        $this->assertDatabaseHas('memberships', [
            'onboarding_enabled' => false,
            'membership_details' => 'BHA 2134',
            'joined_at' => '1997-06-13 18:15:35',
            'user_id' => User::firstWhere('email', 'hayes.ozella@example.com')->id,
        ]);

        $this->assertDatabaseHas('membership_singer_status', [
            'membership_id' => User::firstWhere('email', 'hayes.ozella@example.com')->membership->id,
            'status' => SingerStatus::MEMBERS->value,
        ]);

        // assert roles assigned
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => User::firstWhere('email', 'hayes.ozella@example.com')->membership->id,
            'role_id' => Role::firstWhere('name', 'Admin')->id,
        ]);
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => User::firstWhere('email', 'hayes.ozella@example.com')->membership->id,
            'role_id' => Role::firstWhere('name', 'Music Team')->id,
        ]);
    }

    public function test_groupanizer_import_creates_users(): void
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

        // assert user created
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

        // assert membership created
        $this->assertDatabaseHas('memberships', [
            'onboarding_enabled' => false,
            'membership_details' => 'BHA  1945 Blenders old No 245',
            'joined_at' => '2007-01-31 00:00:00',
            'user_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->id,
        ]);

        $this->assertDatabaseHas('membership_singer_status', [
            'membership_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->membership->id,
            'status' => SingerStatus::MEMBERS->value,
        ]);

        // assert roles assigned
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->membership->id,
            'role_id' => Role::firstWhere('name', 'Admin')->id,
        ]);
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => User::firstWhere('email', 'jonoalbo7@gmail.com')->membership->id,
            'role_id' => Role::firstWhere('name', 'Music Team')->id,
        ]);
    }

    public function test_groupanizer_import_updates_existing_users(): void
    {
        $this->markTestIncomplete();
    }

    public function test_harmonysite_import_creates_users(): void
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

        // assert user created
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

        // assert membership created
        $this->assertDatabaseHas('memberships', [
            'user_id' => User::firstWhere('email', 'nick.s@internode.on.net')->id,
            'onboarding_enabled' => false,
            'joined_at' => '2015-07-17 00:00:00',
        ]);

        $this->assertDatabaseHas('membership_singer_status', [
            'membership_id' => User::firstWhere('email', 'nick.s@internode.on.net')->membership->id,
            'status' => SingerStatus::MEMBERS->value,
        ]);
    }

    public function test_harmonysite_import_assigns_roles_to_singers(): void
    {
        $this->markTestIncomplete();
    }

    public function test_harmonysite_import_updates_existing_users(): void
    {
        $this->markTestIncomplete();
    }

    public function test_blank_choirconcierge_template_returns_validation_error(): void
    {
        $this->actingAs(
            $this->createUserWithRole('Admin')
        );

        // Download the template CSV
        $download = $this->get(the_tenant_route('singers.import.template'));
        $download->assertOk();

        $csv = $download->getContent();

        // Write to a temporary file to simulate an uploaded file
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);
        file_put_contents($meta['uri'], $csv);

        $file = new UploadedFile(
            $meta['uri'],
            'choirconcierge-singers-template.csv',
            'text/csv',
            null,
            true
        );

        $this->post(the_tenant_route('singers.import'), [
            'import_csv' => [$file],
        ])->assertSessionHasErrors(['import_csv.0']);

        fclose($tmp);
    }

    public function test_import_preview_returns_sample_data(): void
    {
        $file = new UploadedFile(
            base_path('tests/files/harmonysite-singers.csv'),
            'harmonysite-singers.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->actingAs(
            $this->createUserWithRole('Admin')
        )
            ->post(the_tenant_route('singers.import.preview'), [
                'import_csv' => [$file],
            ]);

        $response->assertStatus(302)
            ->assertSessionHas('preview');

        $preview = session('preview');
        $this->assertArrayHasKey('data', $preview);
        $this->assertArrayHasKey('total', $preview);
        $this->assertCount(1, $preview['data']); // harmonysite-singers.csv has 2 rows of data
    }
}
