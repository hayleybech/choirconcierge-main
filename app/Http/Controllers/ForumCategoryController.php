<?php

namespace App\Http\Controllers;

use App\Models\Ensemble;
use App\Models\ForumCategory;
use App\Models\ForumReaction;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ForumCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ForumCategory::class, 'forum_category');
    }

    public function index(): Response
    {
        $user = auth()->user();
        $canManageCategories = $user->can('create', ForumCategory::class);
        $categories = ForumCategory::query()
            ->when(! $canManageCategories, fn (Builder $query) => $query->visibleTo($user))
            ->with('ensembles:id,name')
            ->orderBy('title')
            ->get()
            ->values();
        $visibleCategoryIds = $canManageCategories
            ? ForumCategory::query()->visibleTo($user)->pluck('id')
            : $categories->pluck('id');
        $membershipId = $user->membership->id;
        $topicsByCategory = ForumTopic::query()
            ->whereIn('forum_category_id', $visibleCategoryIds)
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
            ->groupBy('forum_category_id');
        $categories = $categories->map(fn (ForumCategory $category): array => [
            'id' => $category->id,
            'title' => $category->title,
            'is_choir_wide' => $category->is_choir_wide,
            'ensembles' => $category->ensembles->map(fn (Ensemble $ensemble): array => [
                'id' => $ensemble->id,
                'name' => $ensemble->name,
            ]),
            'can_view_topics' => $visibleCategoryIds->contains($category->id),
            'topics' => $visibleCategoryIds->contains($category->id) ? $topicsByCategory->get($category->id, collect())
                ->map(fn (ForumTopic $topic): array => $this->topicListItem($topic, $membershipId))
                ->values() : collect(),
        ]);

        return Inertia::render('Forum/Categories/Index', [
            'categories' => $categories,
            'ensembles' => $canManageCategories ? Ensemble::query()->orderBy('name')->get(['id', 'name'])->values() : [],
            'canManageCategories' => $canManageCategories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules($request));
        $category = ForumCategory::create([
            'title' => $data['title'],
            'is_choir_wide' => $data['is_choir_wide'],
        ]);
        $category->ensembles()->sync($data['is_choir_wide'] ? [] : $data['ensemble_ids']);

        return back()->with(['status' => 'Forum category created.']);
    }

    public function update(Request $request, ForumCategory $forum_category): RedirectResponse
    {
        $data = $request->validate($this->rules($request));
        $forum_category->update([
            'title' => $data['title'],
            'is_choir_wide' => $data['is_choir_wide'],
        ]);
        $forum_category->ensembles()->sync($data['is_choir_wide'] ? [] : $data['ensemble_ids']);

        return back()->with(['status' => 'Forum category saved.']);
    }

    public function destroy(ForumCategory $forum_category): RedirectResponse
    {
        $forum_category->delete();

        return back()->with(['status' => 'Forum category deleted.']);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(Request $request): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'is_choir_wide' => ['required', 'boolean'],
            'ensemble_ids' => $request->boolean('is_choir_wide')
                ? ['nullable', 'array']
                : ['required', 'array', 'min:1'],
            'ensemble_ids.*' => [
                'integer',
                Rule::exists('ensembles', 'id')->where('tenant_id', tenant('id')),
            ],
        ];
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
}
