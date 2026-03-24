<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Models\Ensemble;
use App\Models\Folder;
use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Folder::class);
    }

    public function index(): Response
    {
        $user = auth()->user();
        $userEnsembles = $user?->membership?->enrolments->pluck('ensemble_id');

        $folders = Folder::with([
            'documents' => static function ($query) {
                $query->orderBy('title'); // documents by document title
            },
        ])
            ->when(! $user->membership->hasRole('Admin') && ! $user?->isSuperAdmin, function (Builder $query) use ($user, $userEnsembles) {
                $query->where(function (Builder $query) use ($userEnsembles) {
                    $query->whereDoesntHave('ensembles')
                        ->orWhereHas('ensembles', function (Builder $query) use ($userEnsembles) {
                            $query->whereIn('ensembles.id', $userEnsembles ?? []);
                        });
                })
                ->where(function (Builder $query) use ($user) {
                    $query->whereDoesntHave('viewers')
                        ->orWhere(function (Builder $query) use ($user) {
                            $query->whereHas('viewer_users', fn($q) => $q->where('users.id', $user->id))
                                ->orWhereHas('viewer_roles', fn($q) => $q->whereIn('roles.id', $user->membership->roles->pluck('id')))
                                ->orWhereHas('viewer_voice_parts', fn($q) => $q->whereIn('voice_parts.id', $user->membership->enrolments->pluck('voice_part_id')))
                                ->orWhereHas('viewer_singer_categories', fn($q) => $q->where('singer_categories.id', $user->membership->singer_category_id));
                        });
                });
            })
            ->orderBy('title')
            ->get(); // folders by folder title

        $totalEnsemblesCount = Ensemble::count();
        $userEnsemblesCount = ($user?->isSuperAdmin || $user->membership->hasRole('Admin'))
            ? $totalEnsemblesCount
            : $user?->membership?->enrolments->count() ?? 0;

        $ensembles = Ensemble::query()
            ->when(! ($user?->isSuperAdmin || $user->membership->hasRole('Admin')), function (Builder $query) {
                $query->whereIn('id', auth()->user()?->membership?->enrolments->pluck('ensemble_id') ?? []);
            })
            ->get();

        return Inertia::render('Folders/Index', [
            'folders' => $folders->values(),
            'userEnsemblesCount' => $userEnsemblesCount,
            'ensembles' => $ensembles,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Folders/Create', [
            'ensembles' => Ensemble::all()->values(),
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
            'voiceParts' => VoicePart::all()->values(),
            'singerCategories' => SingerCategory::all()->values(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FolderRequest $request
     * @return RedirectResponse
     */
    public function store(FolderRequest $request): RedirectResponse
    {
        Folder::create($request->validated());

        return redirect()
            ->route('folders.index')
            ->with(['status' => 'Folder created. ']);
    }

    public function edit(Folder $folder): Response
    {
        $folder->load([
            'viewer_users', 'viewer_roles', 'viewer_voice_parts', 'viewer_singer_categories',
            'editor_users', 'editor_roles', 'editor_voice_parts', 'editor_singer_categories',
        ]);

        return Inertia::render('Folders/Edit', [
            'folder' => $folder,
            'ensembles' => Ensemble::all()->values(),
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
            'voiceParts' => VoicePart::all()->values(),
            'singerCategories' => SingerCategory::all()->values(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FolderRequest $request
     * @param Folder $folder
     * @return RedirectResponse
     */
    public function update(FolderRequest $request, Folder $folder): RedirectResponse
    {
        $folder->update($request->validated());

        return redirect()
            ->route('folders.index')
            ->with(['status' => 'Folder updated. ']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Folder $folder
     * @return RedirectResponse
     */
    public function destroy(Folder $folder): RedirectResponse
    {
        $folder->delete();

        return redirect()
            ->route('folders.index')
            ->with(['status' => 'Folder deleted.']);
    }
}
