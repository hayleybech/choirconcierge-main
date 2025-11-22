<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChangelogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Central/Changelog', [
            'logs' => Str::of(Storage::disk('assets')->get('changelog.md'))
                ->explode('---'.PHP_EOL)
                ->map(function ($log) {
                    $lines = Str::of($log)->explode(PHP_EOL);
                    return [
                        'date' => Str::of($lines[0])->replaceFirst('# ', ''),
                        'heading' => Str::of($lines[1])->replaceFirst('## ', ''),
                        'content' => Str::markdown($lines->slice(2)->join(PHP_EOL)),
                    ];
                }),
        ]);

    }
}
