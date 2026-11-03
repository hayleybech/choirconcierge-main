<?php

use App\Models\ForumCategory;
use App\Models\ForumReaction;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Models\ForumTopicFollow;
use App\Models\Membership;
use Database\Seeders\Dummy\DummyForumCategorySeeder;
use Database\Seeders\Dummy\DummyForumTopicSeeder;

test('seeds repeatable forum categories, topics, replies, reactions and follows', function () {
    Membership::factory()->count(5)->create();

    $this->seed(DummyForumCategorySeeder::class);
    $this->seed(DummyForumTopicSeeder::class);

    $categoryTitles = ['Rehearsal & Performance', 'Music & Repertoire', 'Choir Community'];
    $getSeededCounts = function () use ($categoryTitles): array {
        $categoryIds = ForumCategory::query()->whereIn('title', $categoryTitles)->pluck('id');
        $topicIds = ForumTopic::query()->whereIn('forum_category_id', $categoryIds)->pluck('id');

        return [
            'categories' => $categoryIds->count(),
            'topics' => $topicIds->count(),
            'replies' => ForumReply::query()->whereIn('forum_topic_id', $topicIds)->count(),
            'nested_replies' => ForumReply::query()->whereIn('forum_topic_id', $topicIds)->whereNotNull('parent_id')->count(),
            'reactions' => ForumReaction::query()->count(),
            'follows' => ForumTopicFollow::query()->count(),
        ];
    };

    $firstSeedCounts = $getSeededCounts();

    $this->seed(DummyForumCategorySeeder::class);
    $this->seed(DummyForumTopicSeeder::class);

    expect($firstSeedCounts)
        ->toBe($getSeededCounts())
        ->and($firstSeedCounts['categories'])->toBe(3)
        ->and($firstSeedCounts['topics'])->toBe(6)
        ->and($firstSeedCounts['replies'])->toBeGreaterThan(0)
        ->and($firstSeedCounts['nested_replies'])->toBeGreaterThan(0)
        ->and($firstSeedCounts['reactions'])->toBeGreaterThan(0)
        ->and($firstSeedCounts['follows'])->toBeGreaterThan(0);
});
