<?php

namespace App\Http\Controllers;

use App\Imports\SingersImport;
use Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportSingerController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if( ! optional(Auth::user())->hasRole('Admin')) {
            abort(403);
        }
        $request->validate([
            'import_csv' => 'required|file',
        ]);

        Excel::import(new SingersImport, request()->file('import_csv'));

        return redirect()->route('singers.index')->with(['status' => 'Import completed. ']);
    }
}
