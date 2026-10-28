<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ForumTopicController extends Controller
{
    public function index(ForumCategory $forumCategory): Response
    {
        $this->authorize('view', $forumCategory);

        $user = request()->user();
        $topics = $forumCategory->topics()
            ->with(['authorMembership.user', 'category'])
            ->withCount('replies')
            ->orderByDesc('is_pinned')
            ->latest()
            ->get()
            ->map(fn (ForumTopic $topic): array => [
                'id' => $topic->id,
                'title' => $topic->title,
                'body_html' => $topic->body_html,
                'created_at' => $topic->created_at->toIso8601String(),
                'author_name' => $topic->authorMembership?->user?->name ?? 'Former member',
                'replies_count' => $topic->replies_count,
                'is_pinned' => $topic->is_pinned,
            ]);

        return Inertia::render('Forum/Topics/Index', [
            'category' => ['id' => $forumCategory->id, 'title' => $forumCategory->title],
            'topics' => $topics,
            'canCreateTopic' => $user->can('create', [ForumTopic::class, $forumCategory]),
        ]);
    }

    public function store(Request $request, ForumCategory $forumCategory): RedirectResponse
    {
        $this->authorize('create', [ForumTopic::class, $forumCategory]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
        ]);

        $topic = $forumCategory->topics()->create([
            ...$data,
            'membership_id' => $request->user()->membership->id,
        ]);

        return redirect()->route('forum.topics.show', ['forum_topic' => $topic]);
    }

    public function show(int $forumTopic): Response
    {
        $topic = ForumTopic::withTrashed()
            ->with(['category', 'authorMembership.user'])
            ->findOrFail($forumTopic);
        $this->authorize('view', $topic);

        $user = request()->user();
        $canEditTopic = $user->can('update', $topic);
        $canModerate = $user->can('moderate', $topic);
        $replies = $topic->replies()
            ->withTrashed()
            ->with(['authorMembership.user', 'topic.category'])
            ->oldest()
            ->get()
            ->map(function (ForumReply $reply) use ($canModerate, $user): array {
                $bodyHtml = $reply->body_html;
                $canEditReply = $user->can('update', $reply);
                $canRestoreReply = $user->can('restore', $reply);

                if ($reply->trashed() && ! $canModerate) {
                    $bodyHtml = '<p>This reply has been removed.</p>';
                }

                return [
                    'id' => $reply->id,
                    'parent_id' => $reply->parent_id,
                    'body_html' => $bodyHtml,
                    'body' => $canEditReply ? $reply->body : null,
                    'author_name' => $reply->authorMembership?->user?->name ?? 'Former member',
                    'created_at' => $reply->created_at->toIso8601String(),
                    'is_deleted' => $reply->trashed(),
                    'can_edit' => $canEditReply,
                    'can_restore' => $canRestoreReply,
                ];
            });

        return Inertia::render('Forum/Topics/Show', [
            'category' => ['id' => $topic->category?->id, 'title' => $topic->category?->title],
            'topic' => [
                'id' => $topic->id,
                'title' => $topic->title,
                'body_html' => $topic->body_html,
                'body' => $canEditTopic ? $topic->body : null,
                'author_name' => $topic->authorMembership?->user?->name ?? 'Former member',
                'created_at' => $topic->created_at->toIso8601String(),
                'is_locked' => $topic->is_locked,
                'is_pinned' => $topic->is_pinned,
                'is_deleted' => $topic->trashed(),
                'can_edit' => $canEditTopic,
            ],
            'replies' => $replies,
            'canModerate' => $canModerate,
            'canReply' => ! $topic->is_locked && ! $topic->trashed()
                && $user->can('create', [ForumReply::class, $topic]),
        ]);
    }

    public function update(Request $request, ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('update', $forumTopic);

        $forumTopic->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
        ]));

        return back()->with(['status' => 'Discussion saved.']);
    }

    public function destroy(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('delete', $forumTopic);
        $forumTopic->delete();

        return redirect()
            ->route('forum.categories.topics.index', ['forum_category' => $forumTopic->forum_category_id])
            ->with(['status' => 'Discussion removed.']);
    }

    public function restore(int $forumTopic): RedirectResponse
    {
        $topic = ForumTopic::withTrashed()->findOrFail($forumTopic);
        $this->authorize('restore', $topic);
        $topic->restore();

        return back()->with(['status' => 'Discussion restored.']);
    }

    public function lock(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('lock', $forumTopic);
        $forumTopic->update(['is_locked' => ! $forumTopic->is_locked]);

        return back()->with(['status' => $forumTopic->is_locked ? 'Discussion locked.' : 'Discussion unlocked.']);
    }

    public function pin(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('pin', $forumTopic);
        $forumTopic->update(['is_pinned' => ! $forumTopic->is_pinned]);

        return back()->with(['status' => $forumTopic->is_pinned ? 'Discussion pinned.' : 'Discussion unpinned.']);
    }

    public function storeReply(Request $request, ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('create', [ForumReply::class, $forumTopic]);

        if ($forumTopic->is_locked) {
            throw ValidationException::withMessages(['topic' => 'This discussion is locked.']);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:20000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('forum_replies', 'id')->where(fn (Builder $query) => $query
                    ->where('forum_topic_id', $forumTopic->id)
                    ->whereNull('parent_id')
                    ->whereNull('deleted_at')),
            ],
        ]);

        $forumTopic->replies()->create([
            ...$data,
            'membership_id' => $request->user()->membership->id,
        ]);

        return back()->with(['status' => 'Reply posted.']);
    }

    public function updateReply(Request $request, ForumReply $forumReply): RedirectResponse
    {
        $this->authorize('update', $forumReply);
        $forumReply->update($request->validate([
            'body' => ['required', 'string', 'max:20000'],
        ]));

        return back()->with(['status' => 'Reply saved.']);
    }

    public function destroyReply(ForumReply $forumReply): RedirectResponse
    {
        $this->authorize('delete', $forumReply);
        $forumReply->delete();

        return back()->with(['status' => 'Reply removed.']);
    }

    public function restoreReply(int $forumReply): RedirectResponse
    {
        $reply = ForumReply::withTrashed()->findOrFail($forumReply);
        $this->authorize('restore', $reply);
        $reply->restore();

        return back()->with(['status' => 'Reply restored.']);
    }
}

