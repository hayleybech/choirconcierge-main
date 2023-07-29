<?php

namespace App\Http\Controllers;

use App\Imports\ChoirConciergeSingersImport;
use App\Imports\GroupanizerSingersImport;
use App\Imports\HarmonysiteSingersImport;
use Excel;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\HeadingRowImport;

class ImportSingerController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()?->isSuperAdmin && ! Auth::user()?->membership?->hasRole('Admin'), 403);

        $request->validate([
            'import_csv.*' => 'required|file',
        ]);

        $csv = request()->file('import_csv')[0];

        $headings = (new HeadingRowImport)->toArray($csv)[0][0];

        Excel::import($this->getImporter($headings), $csv);

        return redirect()
            ->route('singers.index')
            ->with(['status' => 'Import completed. ']);
    }

    /**
     * @throws Exception
     */
    private function getImporter(array $headings): GroupanizerSingersImport|ChoirConciergeSingersImport|HarmonysiteSingersImport
    {
        return match(true) {
            in_array('bha_id', $headings, true) => new ChoirConciergeSingersImport(),
            in_array('user_id', $headings, true) => new GroupanizerSingersImport(),
            in_array('email_address', $headings, true) => new HarmonysiteSingersImport(),
            default => throw new Exception('Could not determine the import type', 422)
        };
    }
}
