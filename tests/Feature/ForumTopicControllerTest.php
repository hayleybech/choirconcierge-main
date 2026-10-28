<?php

use App\Enums\SingerStatus;
use App\Models\Ensemble;
use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

function createForumTopicMember(array $abilities = []): Membership
{
    $role = Role::create([
        'name' => 'Forum topic role '.fake()->unique()->word(),
        'abilities' => $abilities,
    ]);
    $membership = Membership::factory()->create();
    $membership->roles()->attach($role);

    return $membership;
}

function createForumTopic(ForumCategory $category, Membership $author, array $attributes = []): ForumTopic
{
    return ForumTopic::create(array_merge([
        'forum_category_id' => $category->id,
        'membership_id' => $author->id,
        'title' => 'Rehearsal plans',
        'body' => 'Let us plan the next rehearsal.',
    ], $attributes));
}

test('active members create accessible topics and safely rendered replies', function () {
    $membership = createForumTopicMember();
    $category = ForumCategory::factory()->create();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.categories.topics.store', ['forum_category' => $category]), [
            'title' => 'Rehearsal plans',
            'body' => "Welcome **choir**.\n\n<script>alert(1)</script>\n\n[unsafe](javascript:alert(1))",
        ])
        ->assertRedirect();

    $topic = ForumTopic::query()->where('title', 'Rehearsal plans')->firstOrFail();

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.categories.topics.index', ['forum_category' => $category]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Forum/Topics/Index')
            ->has('topics', 1)
        );

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => "A reply with *emphasis*.\n\n<script>alert(2)</script>",
        ])
        ->assertRedirect();

    $reply = ForumReply::query()->where('forum_topic_id', $topic->id)->firstOrFail();

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.topics.show', ['forum_topic' => $topic]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('topic.body_html', $topic->body_html)
            ->has('replies', 1)
            ->where('replies.0.body_html', $reply->body_html)
        );

    expect($topic->body_html)
        ->toContain('<strong>choir</strong>')
        ->not->toContain('<script>')
        ->not->toContain('javascript:');

    expect($reply->body_html)
        ->toContain('<em>emphasis</em>')
        ->not->toContain('<script>');
});

test('members cannot create topics in categories they cannot access', function () {
    $membership = createForumTopicMember();
    $ensemble = Ensemble::factory()->create();
    $category = ForumCategory::factory()->create(['is_choir_wide' => false]);
    $category->ensembles()->attach($ensemble);

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.categories.topics.store', ['forum_category' => $category]), [
            'title' => 'Restricted discussion',
            'body' => 'This should not be stored.',
        ])
        ->assertForbidden();

    expect(ForumTopic::query()->exists())->toBeFalse();

    $topic = createForumTopic($category, $membership);

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.topics.show', ['forum_topic' => $topic]))
        ->assertForbidden();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => 'This reply must not be stored.',
        ])
        ->assertForbidden();

    expect($topic->replies()->count())->toBe(0);
});

test('members can reply one level deep but cannot reply to a nested reply', function () {
    $membership = createForumTopicMember();
    $topic = createForumTopic(ForumCategory::factory()->create(), $membership);

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => 'First reply',
        ])
        ->assertRedirect();

    $firstReply = ForumReply::query()->firstOrFail();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => 'Nested reply',
            'parent_id' => $firstReply->id,
        ])
        ->assertRedirect();

    $nestedReply = ForumReply::query()->whereNotNull('parent_id')->firstOrFail();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => 'Reply that would exceed the limit',
            'parent_id' => $nestedReply->id,
        ])
        ->assertSessionHasErrors('parent_id');

    $otherTopic = createForumTopic(ForumCategory::factory()->create(), $membership, ['title' => 'Another discussion']);
    $otherTopicReply = ForumReply::create([
        'forum_topic_id' => $otherTopic->id,
        'membership_id' => $membership->id,
        'body' => 'A reply in a different topic.',
    ]);

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => 'Cross-topic reply',
            'parent_id' => $otherTopicReply->id,
        ])
        ->assertSessionHasErrors('parent_id');

    expect(ForumReply::query()->count())->toBe(3);
});

test('authors manage their content and moderators can edit remove and restore topics and replies', function () {
    $author = createForumTopicMember();
    $otherMember = createForumTopicMember();
    $moderator = createForumTopicMember(['forum_moderate']);
    $topic = createForumTopic(ForumCategory::factory()->create(), $author);

    $this->actingAs($otherMember->user)
        ->put(the_tenant_route('forum.topics.update', ['forum_topic' => $topic]), [
            'title' => 'Unauthorized edit',
            'body' => 'Not allowed.',
        ])
        ->assertForbidden();

    $this->actingAs($author->user)
        ->put(the_tenant_route('forum.topics.update', ['forum_topic' => $topic]), [
            'title' => 'Author edit',
            'body' => 'Updated by the author.',
        ])
        ->assertRedirect();

    expect($topic->fresh()->title)->toBe('Author edit');

    $this->actingAs($author->user)
        ->delete(the_tenant_route('forum.topics.destroy', ['forum_topic' => $topic]))
        ->assertRedirect();

    expect($topic->fresh()->trashed())->toBeTrue();

    $this->actingAs($otherMember->user)
        ->post(the_tenant_route('forum.topics.restore', ['forum_topic' => $topic->id]))
        ->assertForbidden();

    $this->actingAs($moderator->user)
        ->post(the_tenant_route('forum.topics.restore', ['forum_topic' => $topic->id]))
        ->assertRedirect();

    expect($topic->fresh()->trashed())->toBeFalse();

    $this->actingAs($moderator->user)
        ->put(the_tenant_route('forum.topics.update', ['forum_topic' => $topic]), [
            'title' => 'Moderator edit',
            'body' => 'Updated by a moderator.',
        ])
        ->assertRedirect();

    expect($topic->fresh()->title)->toBe('Moderator edit');

    $this->actingAs($moderator->user)
        ->delete(the_tenant_route('forum.topics.destroy', ['forum_topic' => $topic]))
        ->assertRedirect();

    expect($topic->fresh()->trashed())->toBeTrue();

    $this->actingAs($moderator->user)
        ->post(the_tenant_route('forum.topics.restore', ['forum_topic' => $topic->id]))
        ->assertRedirect();

    expect($topic->fresh()->trashed())->toBeFalse();

    $reply = ForumReply::create([
        'forum_topic_id' => $topic->id,
        'membership_id' => $author->id,
        'body' => 'Author reply',
    ]);

    $this->actingAs($otherMember->user)
        ->put(the_tenant_route('forum.replies.update', ['forum_reply' => $reply]), [
            'body' => 'Unauthorized reply edit',
        ])
        ->assertForbidden();

    $this->actingAs($author->user)
        ->put(the_tenant_route('forum.replies.update', ['forum_reply' => $reply]), [
            'body' => 'Edited author reply',
        ])
        ->assertRedirect();

    expect($reply->fresh()->body)->toBe('Edited author reply');

    $this->actingAs($moderator->user)
        ->put(the_tenant_route('forum.replies.update', ['forum_reply' => $reply]), [
            'body' => 'Edited by moderator',
        ])
        ->assertRedirect();

    expect($reply->fresh()->body)->toBe('Edited by moderator');

    $this->actingAs($author->user)
        ->delete(the_tenant_route('forum.replies.destroy', ['forum_reply' => $reply]))
        ->assertRedirect();

    expect($reply->fresh()->trashed())->toBeTrue();

    $this->actingAs($otherMember->user)
        ->get(the_tenant_route('forum.topics.show', ['forum_topic' => $topic]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('replies.0.body_html', '<p>This reply has been removed.</p>')
        );

    $this->actingAs($moderator->user)
        ->post(the_tenant_route('forum.replies.restore', ['forum_reply' => $reply->id]))
        ->assertRedirect();

    expect($reply->fresh()->trashed())->toBeFalse();

    $this->actingAs($moderator->user)
        ->delete(the_tenant_route('forum.replies.destroy', ['forum_reply' => $reply]))
        ->assertRedirect();

    expect($reply->fresh()->trashed())->toBeTrue();

    $this->actingAs($moderator->user)
        ->post(the_tenant_route('forum.replies.restore', ['forum_reply' => $reply->id]))
        ->assertRedirect();

    expect($reply->fresh()->trashed())->toBeFalse();
});

test('only moderators lock and pin topics while locked topics remain readable', function () {
    $membership = createForumTopicMember();
    $moderator = createForumTopicMember(['forum_moderate']);
    $topic = createForumTopic(ForumCategory::factory()->create(), $membership);

    $this->actingAs($membership->user)
        ->put(the_tenant_route('forum.topics.lock', ['forum_topic' => $topic]))
        ->assertForbidden();

    $this->actingAs($moderator->user)
        ->put(the_tenant_route('forum.topics.lock', ['forum_topic' => $topic]))
        ->assertRedirect();

    expect($topic->fresh()->is_locked)->toBeTrue();

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.topics.show', ['forum_topic' => $topic]))
        ->assertOk();

    $this->actingAs($membership->user)
        ->post(the_tenant_route('forum.topics.replies.store', ['forum_topic' => $topic]), [
            'body' => 'A reply after lock',
        ])
        ->assertSessionHasErrors('topic');

    $this->actingAs($moderator->user)
        ->put(the_tenant_route('forum.topics.pin', ['forum_topic' => $topic]))
        ->assertRedirect();

    expect($topic->fresh()->is_pinned)->toBeTrue();
});

test('topic routes do not expose content from another tenant', function () {
    $membership = createForumTopicMember();
    $currentTenant = tenant();
    $otherTenant = Tenant::factory()->create(['id' => 'other-forum-tenant']);

    tenancy()->initialize($otherTenant);
    $otherCategory = ForumCategory::factory()->create();
    $otherMembership = Membership::factory()->create();
    $otherTopic = createForumTopic($otherCategory, $otherMembership);
    tenancy()->initialize($currentTenant);

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.topics.show', ['forum_topic' => $otherTopic->id]))
        ->assertNotFound();
});

test('inactive members cannot read forum topics', function () {
    $membership = createForumTopicMember();
    $membership->statuses()->create(['status' => SingerStatus::INACTIVE_MEMBERS->value]);
    $topic = createForumTopic(ForumCategory::factory()->create(), $membership);

    $this->actingAs($membership->user)
        ->get(the_tenant_route('forum.topics.show', ['forum_topic' => $topic]))
        ->assertForbidden();
});
