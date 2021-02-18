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

        $category = $request->input('move_category', 0);

        if( $category === 0 ) return redirect()->route('singers.index')->with([ 'status' => 'No category selected. ', 'fail' => true ]);

        // Attach to Prospects category
        $singer->category()->associate($category);
        $singer->save();

        return redirect()->route('singers.index')->with(['status' => 'The singer was moved. ']);
    }
}
