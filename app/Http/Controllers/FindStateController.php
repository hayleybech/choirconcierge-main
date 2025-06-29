<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use MenaraSolutions\Geographer\Earth;
use Illuminate\Support\Facades\Response;

class FindStateController extends Controller
{
    public function __invoke(string $country): JsonResponse {
        if(empty($country)) {
            return Response::json([]);
        }

        return Response::json(
            collect((new Earth())->findOne(['code3' => $country])->getStates())
                ->map(fn($state) => [
                    'label' => $state->name, // Western Australia
                    'value' => $state->getIsoCode(), // AU-WA
                ])
                ->sortBy('label')
                ->values()
        );
    }
}
