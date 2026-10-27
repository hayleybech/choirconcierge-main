<?php

use App\Enums\SingerStatus;
use App\Models\Ensemble;
use App\Models\ForumCategory;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

function createForumMember(array $abilities = []): Membership
{
    $role = Role::create([
        'name' => 'Forum role '.fake()->unique()->word(),
        'abilities' => $abilities,
    ]);
    $membership = Membership::factory()->create();
    $membership->roles()->attach($role);

    return $membership;
}

test('active members see choir-wide and matching ensemble categories only', function () {
    $matchingEnsemble = Ensemble::factory()->create(['name' => 'Alto Ensemble']);
    $otherEnsemble = Ensemble::factory()->create(['name' => 'Tenor Ensemble']);
    $membership = createForumMember();
    $membership->enrolments()->create([
        'ensemble_id' => $matchingEnsemble->id,
        'voice_part_id' => VoicePart::factory()->create()->id,
    ]);

    $choirWideCategory = ForumCategory::create(['title' => 'Choir-wide']);
    $matchingCategory = ForumCategory::create(['title' => 'Alto only', 'is_choir_wide' => false]);
    $matchingCategory->ensembles()->attach($matchingEnsemble);
    ForumCategory::create(['title' => 'Tenor only', 'is_choir_wide' => false])
        ->ensembles()
        ->attach($otherEnsemble);

    $user = $membership->user;

    expect($user->can('view', $choirWideCategory))->toBeTrue()
        ->and($user->can('view', $matchingCategory))->toBeTrue()
        ->and($user->can('view', ForumCategory::query()->where('title', 'Tenor only')->firstOrFail()))
        ->toBeFalse();

    $this->actingAs($user)
        ->get(the_tenant_route('forum.categories.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('categories', 2)
            ->where('categories.0.title', $matchingCategory->title)
            ->where('categories.1.title', $choirWideCategory->title)
        );
});

test('inactive memberships cannot access forum categories', function () {
    $membership = createForumMember();
    $membership->statuses()->create(['status' => SingerStatus::INACTIVE_MEMBERS->value]);

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.categories.index'))
        ->assertForbidden();
});

test('members without the category management ability cannot create categories', function () {
    $category = ForumCategory::create(['title' => 'Protected category']);
    $membership = createForumMember();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.categories.store'), ['title' => 'Unauthorized'])
        ->assertForbidden();

    $this->put(the_tenant_route('forum.categories.update', ['forum_category' => $category]), [
        'title' => 'Unauthorized update',
    ])->assertForbidden();

    $this->delete(the_tenant_route('forum.categories.destroy', ['forum_category' => $category]))
        ->assertForbidden();

    expect(ForumCategory::query()->where('title', 'Unauthorized')->exists())->toBeFalse();
    expect($category->refresh()->title)->toBe('Protected category');
});

test('members with the category management ability can create update and delete categories', function () {
    $membership = createForumMember(['forum_categories_manage']);
    $ensemble = Ensemble::factory()->create();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.categories.store'), [
            'title' => 'Rehearsal Planning',
            'is_choir_wide' => false,
            'ensemble_ids' => [$ensemble->id],
        ])
        ->assertRedirect();

    $category = ForumCategory::query()->where('title', 'Rehearsal Planning')->firstOrFail();
    expect($category->ensembles->modelKeys())->toBe([$ensemble->id]);

    $this->put(the_tenant_route('forum.categories.update', ['forum_category' => $category]), [
        'title' => 'Performance Planning',
        'is_choir_wide' => true,
        'ensemble_ids' => [],
    ])->assertRedirect();

    expect($category->refresh()->title)->toBe('Performance Planning')
        ->and($category->is_choir_wide)->toBeTrue()
        ->and($category->ensembles)->toBeEmpty();

    $this->delete(the_tenant_route('forum.categories.destroy', ['forum_category' => $category]))
        ->assertRedirect();

    expect(ForumCategory::withTrashed()->findOrFail($category->id)->trashed())->toBeTrue();
});

test('category listing is scoped to the current tenant', function () {
    $membership = createForumMember();
    $currentTenant = tenant();
    $otherTenant = Tenant::factory()->create(['id' => 'other-choir']);

    tenancy()->initialize($otherTenant);
    ForumCategory::create(['title' => 'Other tenant category']);
    tenancy()->initialize($currentTenant);

    $currentCategory = ForumCategory::create(['title' => 'Current tenant category']);

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.categories.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('categories', 1)
            ->where('categories.0.title', $currentCategory->title)
        );
});

test('category managers cannot assign an ensemble from another tenant', function () {
    $membership = createForumMember(['forum_categories_manage']);
    $currentTenant = tenant();
    $otherTenant = Tenant::factory()->create(['id' => 'other-choir']);

    tenancy()->initialize($otherTenant);
    $otherEnsemble = Ensemble::factory()->create();
    tenancy()->initialize($currentTenant);

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.categories.store'), [
            'title' => 'Invalid category',
            'is_choir_wide' => false,
            'ensemble_ids' => [$otherEnsemble->id],
        ])
        ->assertSessionHasErrors('ensemble_ids.0');

    expect(ForumCategory::query()->where('title', 'Invalid category')->exists())->toBeFalse();
});

test('deleting an ensemble does not make its restricted category choir-wide', function () {
    $membership = createForumMember();
    $ensemble = Ensemble::factory()->create();
    $category = ForumCategory::create([
        'title' => 'Retired ensemble category',
        'is_choir_wide' => false,
    ]);
    $category->ensembles()->attach($ensemble);

    $ensemble->delete();

    expect($membership->user->can('view', $category))->toBeFalse();

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.categories.index'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->has('categories', 0));
});
