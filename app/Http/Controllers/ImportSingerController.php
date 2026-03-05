<?php

namespace App\Http\Controllers;

use App\Imports\ChoirConciergeSingersImport;
use App\Imports\GroupanizerSingersImport;
use App\Imports\HarmonysiteSingersImport;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportSingerController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()?->isSuperAdmin && ! Auth::user()?->membership?->hasRole('Admin'), 403);

        $request->validate([
            'import_csv' => [
                'required',
                'array',
            ],
            'import_csv.*' => [
                'required',
                'file',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $lineCount = count(@file($value->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []);
                    if ($lineCount <= 1) {
                        $fail('The uploaded file contains no data rows.');
                    }
                },
            ],
        ]);

        $csv = $request->file('import_csv')[0];

        $headings = (new HeadingRowImport)->toArray($csv)[0][0];
        Excel::import($this->getImporter($headings), $csv);

        return redirect()
            ->route('singers.index')
            ->with(['status' => 'Import completed. ']);
    }

    public function preview(Request $request)
    {
        abort_if(! Auth::user()?->isSuperAdmin && ! Auth::user()?->membership?->hasRole('Admin'), 403);

        $request->validate([
            'import_csv' => [
                'required',
                'array',
            ],
            'import_csv.*' => [
                'required',
                'file',
            ],
        ]);

        $csv = $request->file('import_csv')[0];

        $data = Excel::toArray(new class implements WithHeadingRow {}, $csv)[0];

        // Return first 5 rows for preview
        return response()->json([
            'data' => array_slice($data, 0, 5),
            'total' => count($data),
        ]);
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
