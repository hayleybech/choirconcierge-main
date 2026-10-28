<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ForumTopicFollowController extends Controller
{
    public function index(): Response
    {
        $user = request()->user();
        $topics = $user->membership->followedForumTopics()
            ->with(['category', 'authorMembership.user'])
            ->latest('forum_topic_follows.created_at')
            ->get()
            ->filter(fn (ForumTopic $topic): bool => $topic->category && $user->can('view', $topic->category))
            ->map(fn (ForumTopic $topic): array => [
                'id' => $topic->id,
                'title' => $topic->title,
                'category_id' => $topic->category->id,
                'category_title' => $topic->category->title,
                'author_name' => $topic->authorMembership?->user?->name ?? 'Former member',
                'created_at' => $topic->created_at->toIso8601String(),
            ])
            ->values();

        return Inertia::render('Forum/Topics/Following', ['topics' => $topics]);
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
