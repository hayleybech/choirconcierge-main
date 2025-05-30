<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use PragmaRX\Countries\Package\Countries;
use Illuminate\Support\Facades\Response;

class FindStateController extends Controller
{
    public function __invoke(string $country): JsonResponse {
        if(empty($country)) {
            return Response::json([]);
        }

        return Response::json(
            Countries::where('cca3', $country)
                ->firstOrFail()
                ->hydrateStates()
                ->states
                ->filter(fn ($state) => !Str::of($state->iso_3166_2)->contains('~')) // Only support territories with a valid ISO 3166-2 code
                ->map(fn ($state) => [
                    'label' => "{$state->name} ({$state->postal})", // Western Australia (WA)
                    'value' => $state->iso_3166_2, // AU-WA
                ])
                ->values()
        );
    }
}
