<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReaction;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ForumTopicFollowController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        $visibleCategoryIds = ForumCategory::query()
            ->visibleTo($user)
            ->pluck('id');
        $topics = $user->membership->followedForumTopics()
            ->whereIn('forum_category_id', $visibleCategoryIds)
            ->with(['category', 'authorMembership.user', 'reactions.membership.user'])
            ->withCount('replies')
            ->latest('forum_topic_follows.created_at')
            ->get()
            ->map(fn (ForumTopic $topic): array => [
                'id' => $topic->id,
                'title' => $topic->title,
                'category_id' => $topic->category->id,
                'category_title' => $topic->category->title,
                'author_name' => $topic->authorMembership?->user?->name ?? 'Former member',
                'author_avatar_url' => $topic->authorMembership?->user?->avatar_url,
                'created_at' => $topic->created_at->toIso8601String(),
                'replies_count' => $topic->replies_count,
                'is_pinned' => $topic->is_pinned,
                'reactions' => $this->reactionsFor($topic, $user->membership->id),
            ])
            ->values();

        return Inertia::render('Forum/Topics/Following', ['topics' => $topics]);
    }

    /** @return array<int, array{emoji: string, count: int, reacted: bool, reactors: array<int, array{id: int, name: string, avatar_url: string}>}> */
    private function reactionsFor(ForumTopic $topic, int $membershipId): array
    {
        return $topic->reactions
            ->groupBy('emoji')
            ->map(fn (Collection $reactions, string $emoji): array => [
                'emoji' => $emoji,
                'count' => $reactions->count(),
                'reacted' => $reactions->contains('membership_id', $membershipId),
                'reactors' => $reactions
                    ->map(function (ForumReaction $reaction): ?array {
                        $user = $reaction->membership?->user;

                        return $user ? [
                            'id' => $reaction->membership_id,
                            'name' => $user->name,
                            'avatar_url' => $user->avatar_url,
                        ] : null;
                    })
                    ->filter()
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    public function follow(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('view', $forumTopic);
        $this->authorize('view', $forumTopic->category);
        abort_if($forumTopic->trashed(), 404);

        request()->user()->membership->forumTopicFollows()->firstOrCreate([
            'forum_topic_id' => $forumTopic->id,
        ]);

        return back();
    }

    public function unfollow(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('view', $forumTopic);
        $this->authorize('view', $forumTopic->category);
        abort_if($forumTopic->trashed(), 404);

        request()->user()->membership->forumTopicFollows()
            ->where('forum_topic_id', $forumTopic->id)
            ->delete();

        return back();
    }
}
