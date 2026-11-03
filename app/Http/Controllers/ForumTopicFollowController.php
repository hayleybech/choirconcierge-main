<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReaction;
use App\Models\ForumTopic;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ForumTopicFollowController extends Controller
{
    public function index(Request $request): Response
    {
        $user = request()->user();
        $membershipId = $user->membership->id;
        $filter = $request->validate([
            'filter' => ['nullable', 'string', Rule::in(['all', 'unread'])],
        ])['filter'] ?? 'all';
        $visibleCategoryIds = ForumCategory::query()
            ->visibleTo($user)
            ->pluck('id');
        $topics = $user->membership->followedForumTopics()
            ->whereIn('forum_category_id', $visibleCategoryIds)
            ->with([
                'category',
                'authorMembership.user',
                'latestReply',
                'reads' => fn (HasMany $query) => $query->where('membership_id', $membershipId),
                'reactions.membership.user',
            ])
            ->withCount('replies')
            ->latest('forum_topic_follows.created_at')
            ->get()
            ->map(function (ForumTopic $topic) use ($membershipId): array {
                $read = $topic->reads->first();

                return [
                    'id' => $topic->id,
                    'title' => $topic->title,
                    'category_id' => $topic->category->id,
                    'category_title' => $topic->category->title,
                    'author_name' => $topic->authorMembership?->user?->name ?? 'Former member',
                    'author_membership_id' => $topic->authorMembership?->user ? $topic->authorMembership->id : null,
                    'author_avatar_url' => $topic->authorMembership?->user?->avatar_url,
                    'created_at' => $topic->created_at->toIso8601String(),
                    'replies_count' => $topic->replies_count,
                    'is_pinned' => $topic->is_pinned,
                    'is_unread' => $read === null || ($topic->latestReply !== null && $topic->latestReply->id > (int) $read->last_read_reply_id),
                    'reactions' => $this->reactionsFor($topic, $membershipId),
                ];
            })
            ->when($filter === 'unread', fn (Collection $topics): Collection => $topics->where('is_unread', true)->values());

        return Inertia::render('Forum/Topics/Following', ['topics' => $topics, 'filter' => $filter]);
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
