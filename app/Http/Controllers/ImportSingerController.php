<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSingerRequest;
use App\Imports\ChoirConciergeSingersImport;
use App\Imports\GroupanizerSingersImport;
use App\Imports\HarmonysiteSingersImport;
use Exception;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Sentry;

class ImportSingerController extends Controller
{
    public function __invoke(ImportSingerRequest $request): RedirectResponse
    {
        $csv = $request->file('import_csv')[0];

        try {
            $headings = (new HeadingRowImport)->toArray($csv)[0][0];
            Excel::import($this->getImporter($headings), $csv);
        }
        catch (ValidationException $e) {
            return back()->withErrors($e->failures());
        }
        catch (Exception $e) {
            Sentry::captureException($e);
            return back()->withErrors(['An error occurred while importing the file. Please try again.']);
        }

        return redirect()
            ->route('singers.index')
            ->with(['status' => 'Import completed. ']);
    }

    public function preview(ImportSingerRequest $request)
    {
        $csv = $request->file('import_csv')[0];

        $data = Excel::toArray(new class implements WithHeadingRow {}, $csv)[0];

        // Return first 5 rows for preview
        return back()->with('preview', [
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
