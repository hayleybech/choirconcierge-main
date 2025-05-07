<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Khsing\World\Models\Country;
use Khsing\World\Models\Division;

class FindLocationController extends Controller
{
    public function state(Country $country, string $keyword): JsonResponse
    {
		dd($country)
        $keyword = trim($keyword);

        if (empty($keyword)) {
            return Response::json([]);
        }

        return Response::json(Division::query()
	        ->whereBelongsTo($country)
            ->where('name', 'LIKE', '%'.$keyword.'%')
            ->get()
            ->map(fn (Division $state) => [
                'value' => $state->id,
                'label' => $state->name,
                'name' => $state->name,
            ])
        );
    }
}
