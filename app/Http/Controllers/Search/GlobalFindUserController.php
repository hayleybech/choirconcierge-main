<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GlobalFindUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = trim($request->q);

        if (empty($term)) {
            return Response::json([]);
        }

        $formatted_results = User::query()
            ->withoutTenancy()
            ->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$term%' OR email LIKE '%$term%'")
            ->limit(5)
            ->get()
            ->map(fn ($user) => [
                'value' => $user->id,
                'label' => $user->name . ' ('.$user->email.')',
                'name' => $user->name,
                'avatarUrl' => $user->avatar_url,
                'email' => $user->email,
            ]);

            return Response::json($formatted_results);
    }
}
