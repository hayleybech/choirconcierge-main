<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FindSingerController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = trim($request->q);

        if (empty($term)) {
            return Response::json([]);
        }

        $formatted_results = Membership::query()
            ->with(['roles', 'user'])
            ->whereHas('user', function (Builder $query) use ($term) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$term%'");
            })
            ->limit(5)
            ->get()
            ->map(function ($singer) {
                $role_string = $singer->roles->count() ? ' ['.$singer->roles->implode('name', ', ').']' : '';

                return [
                    'value' => $singer->user->id,
                    'label' => $singer->user->name.' ('.$singer->user->email.')'.$role_string,
                    'name' => $singer->user->name,
                    'avatarUrl' => $singer->user->avatar_url,
                    'email' => $singer->user->email,
                    'roles' => $singer->roles,
                ];
            });

        return Response::json($formatted_results);
    }
}
