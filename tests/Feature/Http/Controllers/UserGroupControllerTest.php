<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\UserGroup;
use App\Enums\SingerStatus;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\UserGroupController
 */
class UserGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $this->get(the_tenant_route('groups.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Create')
                ->has('roles')
                ->has('voiceParts')
                ->has('singerStatuses')
            );
    }

    public function test_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $this->delete(the_tenant_route('groups.destroy', ['group' => $group]))
            ->assertRedirect(the_tenant_route('groups.index'));

        $this->assertSoftDeleted($group);
    }

    public function test_bulk_destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $groups = UserGroup::factory()->count(3)->create();

        $this->post(the_tenant_route('groups.bulk-destroy'), [
            'group_ids' => $groups->pluck('id')->toArray(),
        ])->assertRedirect(the_tenant_route('groups.index'));

        foreach ($groups as $group) {
            $this->assertSoftDeleted($group);
        }
    }

    public function test_edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $response = $this->get(the_tenant_route('groups.edit', ['group' => $group]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Edit')
                ->has('list')
                ->has('roles')
                ->has('voiceParts')
                ->has('singerStatuses')
            );
    }

    public function test_index_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $response = $this->get(the_tenant_route('groups.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Index')
                ->has('lists')
            );
    }

    public function test_show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $this->get(the_tenant_route('groups.show', ['group' => $group]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('MailingLists/Show')
                ->has('list')
            );
    }

    #[DataProvider('eventProvider')]
    public function test_store_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $data = $getData();
        $response = $this->post(the_tenant_route('groups.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_groups', $data);

        $group = UserGroup::firstWhere('title', $data['title']);
        $response->assertRedirect(the_tenant_route('groups.show', [$group]));
    }

    public function test_can_save_and_display_singer_statuses_for_user_groups(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $statusValue = SingerStatus::PROSPECTS->value;

        // 1. POST request to create a user group with singer statuses
        $this->post(the_tenant_route('groups.store'), [
            'title' => 'Test Group',
            'slug' => 'test-group',
            'list_type' => 'distribution',
            'recipient_singer_statuses' => [$statusValue],
            'sender_singer_statuses' => [$statusValue],
        ])
            ->assertRedirect(); // Usually redirects to show page

        $group = UserGroup::where('title', 'Test Group')->first();
        $this->assertNotNull($group);

        // Verify it's saved in the database
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'memberable_id' => $statusValue,
            'memberable_type' => SingerStatus::class,
        ]);
        $this->assertDatabaseHas('group_senders', [
            'group_id' => $group->id,
            'sender_id' => $statusValue,
            'sender_type' => SingerStatus::class,
        ]);

        // 2. GET request to edit the group and verify Inertia data
        $this->get(the_tenant_route('groups.edit', $group))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('list.recipient_singer_statuses.0.memberable_id', $statusValue)
                ->where('list.sender_singer_statuses.0.sender_id', $statusValue)
            );
    }

    public function test_full_flow_of_all_relationships_for_user_groups(): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $role = \App\Models\Role::factory()->create();
        $voicePart = \App\Models\VoicePart::factory()->create();
        $ensemble = \App\Models\Ensemble::factory()->create();
        $user = \App\Models\User::factory()->create();
        $status = SingerStatus::MEMBERS;

        $data = [
            'title' => 'Full Flow Group',
            'slug' => 'full-flow-group',
            'list_type' => 'distribution',
            'recipient_roles' => [$role->id],
            'recipient_voice_parts' => [$voicePart->id],
            'recipient_ensembles' => [$ensemble->id],
            'recipient_users' => [$user->id],
            'recipient_singer_statuses' => [$status->value],
            'sender_roles' => [$role->id],
            'sender_voice_parts' => [$voicePart->id],
            'sender_ensembles' => [$ensemble->id],
            'sender_users' => [$user->id],
            'sender_singer_statuses' => [$status->value],
        ];

        // 1. Store
        $this->post(the_tenant_route('groups.store'), $data)
            ->assertRedirect();

        $group = UserGroup::where('title', 'Full Flow Group')->first();
        $this->assertNotNull($group);

        // Verify Database Recipients
        $this->assertDatabaseHas('group_members', ['group_id' => $group->id, 'memberable_id' => $role->id, 'memberable_type' => \App\Models\Role::class]);
        $this->assertDatabaseHas('group_members', ['group_id' => $group->id, 'memberable_id' => $voicePart->id, 'memberable_type' => \App\Models\VoicePart::class]);
        $this->assertDatabaseHas('group_members', ['group_id' => $group->id, 'memberable_id' => $ensemble->id, 'memberable_type' => \App\Models\Ensemble::class]);
        $this->assertDatabaseHas('group_members', ['group_id' => $group->id, 'memberable_id' => $user->id, 'memberable_type' => \App\Models\User::class]);
        $this->assertDatabaseHas('group_members', ['group_id' => $group->id, 'memberable_id' => $status->value, 'memberable_type' => SingerStatus::class]);

        // Verify Database Senders
        $this->assertDatabaseHas('group_senders', ['group_id' => $group->id, 'sender_id' => $role->id, 'sender_type' => \App\Models\Role::class]);
        $this->assertDatabaseHas('group_senders', ['group_id' => $group->id, 'sender_id' => $voicePart->id, 'sender_type' => \App\Models\VoicePart::class]);
        $this->assertDatabaseHas('group_senders', ['group_id' => $group->id, 'sender_id' => $ensemble->id, 'sender_type' => \App\Models\Ensemble::class]);
        $this->assertDatabaseHas('group_senders', ['group_id' => $group->id, 'sender_id' => $user->id, 'sender_type' => \App\Models\User::class]);
        $this->assertDatabaseHas('group_senders', ['group_id' => $group->id, 'sender_id' => $status->value, 'sender_type' => SingerStatus::class]);

        // 2. Edit - Verify Display
        $this->get(the_tenant_route('groups.edit', $group))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('list.recipient_roles', 1)
                ->has('list.recipient_voice_parts', 1)
                ->has('list.recipient_ensembles', 1)
                ->has('list.recipient_users', 1)
                ->has('list.recipient_singer_statuses', 1)
                ->has('list.sender_roles', 1)
                ->has('list.sender_voice_parts', 1)
                ->has('list.sender_ensembles', 1)
                ->has('list.sender_users', 1)
                ->has('list.sender_singer_statuses', 1)
            );

        // 3. Update - Clear some and change others
        $newData = $data;
        unset($newData['recipient_roles']);
        $newData['recipient_users'] = [];
        $newData['title'] = 'Updated Full Flow Group';

        $this->put(the_tenant_route('groups.update', $group), $newData)
            ->assertRedirect();

        $group->refresh();
        $this->assertEquals('Updated Full Flow Group', $group->title);
        $this->assertCount(0, $group->recipient_roles);
        $this->assertCount(0, $group->recipient_users);
        $this->assertCount(1, $group->recipient_voice_parts); // Should remain
    }

    #[DataProvider('eventProvider')]
    public function test_update_redirects_to_show($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $group = UserGroup::factory()->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('groups.update', ['group' => $group]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('user_groups', $data);
        $response->assertRedirect(the_tenant_route('groups.show', [$group]));
    }

    public static function eventProvider(): array
    {
        return [
            [
                function () {
                    $faker = Factory::create();

                    return [
                        'title' => $faker->sentence(),
                        'slug' => $faker->unique()->slug(),
                        'list_type' => $faker->randomElement(['public', 'chat', 'distribution']),
                    ];
                },
            ],
        ];
    }
}
