<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateSingerCategoryController extends Controller
{
    public function __invoke(Singer $singer, Request $request): RedirectResponse
    {
        $this->authorize('create', $singer);

        $request->validate(['move_category' => 'required|numeric|gt:0']);

        $category = $request->input('move_category', 0);

        // Attach to Prospects category
        $singer->category()->associate($category);
        $singer->save();

        return redirect()
            ->back()
            ->with(['status' => 'The singer was moved. ']);
    }
}
