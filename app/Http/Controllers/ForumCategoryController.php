<?php

namespace App\Http\Controllers;

use App\Models\Ensemble;
use App\Models\ForumCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $canManageCategories = auth()->user()->can('create', ForumCategory::class);
        $ensembleIds = auth()->user()->membership->enrolments->pluck('ensemble_id');

        $categories = ForumCategory::query()
            ->with('ensembles:id,name')
            ->when(! $canManageCategories, fn (Builder $query) => $query->where(function (Builder $query) use ($ensembleIds) {
                $query->where('is_choir_wide', true)
                    ->orWhereHas('ensembles', fn (Builder $query) => $query->whereIn('ensembles.id', $ensembleIds));
            }))
            ->orderBy('title')
            ->get()
            ->values();

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
}
