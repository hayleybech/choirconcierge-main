<?php

namespace App\Http\Controllers;

use App\Models\SongCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SongCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SongCategory::class, 'song_category');
    }

    public function index(): Response
    {
        return Inertia::render('SongCategories/Index', ['categories' => SongCategory::withCount('songs')->get()->values()]);
    }

    public function store(Request $request): RedirectResponse
    {
        SongCategory::create($request->validate([
            'title' => 'required|max:255',
        ]));

        return redirect()
            ->back()
            ->with(['status' => 'Category created.']);
    }

    public function update(Request $request, SongCategory $song_category): RedirectResponse
    {
        $song_category->update($request->validate([
            'title' => 'required|max:255',
        ]));

        return redirect()
            ->back()
            ->with(['status' => 'Category saved.']);
    }

    public function destroy(SongCategory $song_category): RedirectResponse
    {
        $song_category->delete();

        return redirect()
            ->back()
            ->with(['status' => 'Category deleted.']);
    }
}
