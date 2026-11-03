<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumReaction;
use App\Models\ForumReply;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ForumTopicController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ForumTopic::class);

        $user = request()->user();
        $membershipId = $user->membership->id;
        $filter = $this->validatedTopicFilter($request);
        $categories = $this->visibleCategories($user);
        $topicCategories = $categories
            ->map(fn (ForumCategory $category): array => [
                'id' => $category->id,
                'title' => $category->title,
                'topics_count' => $category->topics_count,
            ])
            ->values();
        $topics = ForumTopic::query()
            ->whereIn('forum_category_id', $categories->pluck('id'))
            ->with([
                'authorMembership.user',
                'category',
                'latestReply.authorMembership.user',
                'reads' => fn (HasMany $query) => $query->where('membership_id', $membershipId),
                'reactions.membership.user',
            ])
            ->withCount('replies')
            ->orderByDesc('is_pinned')
            ->latest()
            ->get()
            ->map(fn (ForumTopic $topic): array => $this->topicListItem($topic, $membershipId))
            ->when($filter === 'unread', fn (Collection $topics): Collection => $topics->where('is_unread', true)->values());

        return Inertia::render('Forum/Topics/Index', [
            'category' => null,
            'topicCategories' => $topicCategories,
            'topics' => $topics,
            'canCreateTopic' => $topicCategories->isNotEmpty(),
            'filter' => $filter,
        ]);
    }

    public function categoryIndex(Request $request, ForumCategory $forumCategory): Response
    {
        $this->authorize('view', $forumCategory);

        $user = request()->user();
        $membershipId = $user->membership->id;
        $filter = $this->validatedTopicFilter($request);
        $categories = $this->visibleCategories($user);
        $topics = $forumCategory->topics()
            ->with([
                'authorMembership.user',
                'category',
                'latestReply.authorMembership.user',
                'reads' => fn (HasMany $query) => $query->where('membership_id', $membershipId),
                'reactions.membership.user',
            ])
            ->withCount('replies')
            ->orderByDesc('is_pinned')
            ->latest()
            ->get()
            ->map(fn (ForumTopic $topic): array => $this->topicListItem($topic, $membershipId))
            ->when($filter === 'unread', fn (Collection $topics): Collection => $topics->where('is_unread', true)->values());

        return Inertia::render('Forum/Topics/Index', [
            'category' => ['id' => $forumCategory->id, 'title' => $forumCategory->title],
            'topicCategories' => $categories
                ->map(fn (ForumCategory $category): array => [
                    'id' => $category->id,
                    'title' => $category->title,
                    'topics_count' => $category->topics_count,
                ])
                ->values(),
            'topics' => $topics,
            'canCreateTopic' => $user->can('create', [ForumTopic::class, $forumCategory]),
            'filter' => $filter,
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
            ->with(['category', 'authorMembership.user', 'latestReply', 'reactions.membership.user'])
            ->findOrFail($forumTopic);
        $this->authorize('view', $topic);

        $user = request()->user();
        $topic->reads()->updateOrCreate(
            ['membership_id' => $user->membership->id],
            [
                'last_read_reply_id' => $topic->latestReply?->id,
                'read_at' => now(),
            ]
        );
        $canEditTopic = $user->can('update', $topic);
        $canModerate = $user->can('moderate', $topic);
        $replies = $topic->replies()
            ->withTrashed()
            ->with(['authorMembership.user', 'topic.category', 'reactions.membership.user'])
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
                    'author_membership_id' => $reply->authorMembership?->user ? $reply->authorMembership->id : null,
                    'created_at' => $reply->created_at->toIso8601String(),
                    'is_deleted' => $reply->trashed(),
                    'can_edit' => $canEditReply,
                    'can_restore' => $canRestoreReply,
                    'reactions' => $this->reactionsFor($reply, $user->membership->id),
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
                'author_membership_id' => $topic->authorMembership?->user ? $topic->authorMembership->id : null,
                'author_avatar_url' => $topic->authorMembership?->user?->avatar_url,
                'created_at' => $topic->created_at->toIso8601String(),
                'is_locked' => $topic->is_locked,
                'is_pinned' => $topic->is_pinned,
                'is_deleted' => $topic->trashed(),
                'can_edit' => $canEditTopic,
                'is_following' => $user->membership->forumTopicFollows()->where('forum_topic_id', $topic->id)->exists(),
                'reactions' => $this->reactionsFor($topic, $user->membership->id),
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

        return back()->with(['status' => 'Topic saved.']);
    }

    public function destroy(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('delete', $forumTopic);
        $forumTopic->delete();

        return redirect()
            ->route('forum.categories.topics.index', ['forum_category' => $forumTopic->forum_category_id])
            ->with(['status' => 'Topic removed.']);
    }

    public function restore(int $forumTopic): RedirectResponse
    {
        $topic = ForumTopic::withTrashed()->findOrFail($forumTopic);
        $this->authorize('restore', $topic);
        $topic->restore();

        return back()->with(['status' => 'Topic restored.']);
    }

    public function lock(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('lock', $forumTopic);
        $forumTopic->update(['is_locked' => ! $forumTopic->is_locked]);

        return back()->with(['status' => $forumTopic->is_locked ? 'Topic locked.' : 'Topic unlocked.']);
    }

    public function pin(ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('pin', $forumTopic);
        $forumTopic->update(['is_pinned' => ! $forumTopic->is_pinned]);

        return back()->with(['status' => $forumTopic->is_pinned ? 'Topic pinned.' : 'Topic unpinned.']);
    }

    public function storeReply(Request $request, ForumTopic $forumTopic): RedirectResponse
    {
        $this->authorize('create', [ForumReply::class, $forumTopic]);

        if ($forumTopic->is_locked) {
            throw ValidationException::withMessages(['topic' => 'This topic is locked.']);
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

    /**
     * @return Collection<int, ForumCategory>
     */
    private function visibleCategories(User $user): Collection
    {
        return ForumCategory::query()
            ->visibleTo($user)
            ->withCount('topics')
            ->orderBy('title')
            ->get()
            ->values();
    }

    private function validatedTopicFilter(Request $request): string
    {
        return $request->validate([
            'filter' => ['nullable', 'string', Rule::in(['all', 'unread'])],
        ])['filter'] ?? 'all';
    }

    /** @return array<string, mixed> */
    private function topicListItem(ForumTopic $topic, int $membershipId): array
    {
        $author = $topic->authorMembership?->user;
        $latestReplyAuthor = $topic->latestReply?->authorMembership?->user;
        $read = $topic->reads->first();

        return [
            'id' => $topic->id,
            'title' => $topic->title,
            'body_html' => $topic->body_html,
            'created_at' => $topic->created_at->toIso8601String(),
            'category_id' => $topic->category?->id,
            'category_title' => $topic->category?->title,
            'author_name' => $author?->name ?? 'Former member',
            'author_membership_id' => $author ? $topic->authorMembership?->id : null,
            'author_avatar_url' => $author?->avatar_url,
            'latest_reply' => $topic->latestReply ? [
                'author_name' => $latestReplyAuthor?->name ?? 'Former member',
                'author_membership_id' => $latestReplyAuthor ? $topic->latestReply->authorMembership?->id : null,
                'author_avatar_url' => $latestReplyAuthor?->avatar_url,
                'created_at' => $topic->latestReply->created_at->toIso8601String(),
            ] : null,
            'replies_count' => $topic->replies_count,
            'is_pinned' => $topic->is_pinned,
            'is_unread' => $read === null || ($topic->latestReply !== null && $topic->latestReply->id > (int) $read->last_read_reply_id),
            'reactions' => $this->reactionsFor($topic, $membershipId),
        ];
    }

    /** @return array<int, array{emoji: string, count: int, reacted: bool, reactors: array<int, array{id: int, name: string, avatar_url: string}>}> */
    private function reactionsFor(ForumTopic|ForumReply $target, int $membershipId): array
    {
        return $target->reactions
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
}
