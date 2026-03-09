<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Models\Ensemble;
use App\Models\Folder;
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
        $userEnsembles = auth()->user()?->membership?->enrolments->pluck('ensemble_id');
        $canUpdate = auth()->user()?->membership?->hasAbility('folders_update');

        $folders = Folder::with([
            'documents' => static function ($query) {
                $query->orderBy('title'); // documents by document title
            },
        ])
            ->when(! $canUpdate && ! auth()->user()?->isSuperAdmin, function (Builder $query) use ($userEnsembles) {
                $query->where(function (Builder $query) use ($userEnsembles) {
                    $query->whereDoesntHave('ensembles')
                        ->orWhereHas('ensembles', function (Builder $query) use ($userEnsembles) {
                            $query->whereIn('ensembles.id', $userEnsembles ?? []);
                        });
                });
            })
            ->orderBy('title')
            ->get(); // folders by folder title

        $totalEnsemblesCount = Ensemble::count();
        $userEnsemblesCount = (auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasAbility('folders_update'))
            ? $totalEnsemblesCount
            : auth()->user()?->membership?->enrolments->count() ?? 0;

        $ensembles = Ensemble::query()
            ->when(! (auth()->user()?->isSuperAdmin || auth()->user()?->membership?->hasAbility('folders_update')), function (Builder $query) {
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
        return Inertia::render('Folders/Edit', [
            'folder' => $folder,
            'ensembles' => Ensemble::all()->values(),
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
