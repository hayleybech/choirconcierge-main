<?php

namespace App\Http\Controllers;

use App\Imports\GroupanizerSingersImport;
use App\Imports\HarmonysiteSingersImport;
use Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\HeadingRowImport;

class ImportSingerController extends Controller
{
	public function __invoke(Request $request): RedirectResponse
	{
		abort_if(! Auth::user()?->singer?->hasRole('Admin'), 403);

		$request->validate([
			'import_csv' => 'required|file',
		]);

        $headings = (new HeadingRowImport)->toArray(request()->file('import_csv'));

        if(array_key_exists('User ID', $headings)){
            Excel::import(new GroupanizerSingersImport(), request()->file('import_csv'));
        } else {
            Excel::import(new HarmonysiteSingersImport(), request()->file('import_csv'));
        }

		return redirect()
			->route('singers.index')
			->with(['status' => 'Import completed. ']);
	}
}
