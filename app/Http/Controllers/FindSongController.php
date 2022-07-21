<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class FindSongController extends Controller
{
    public function __invoke(string $keyword): JsonResponse
    {
        $keyword = trim($keyword);

        if (empty($keyword)) {
            return Response::json([]);
        }

        return Response::json(Song::query()
            ->where('title', 'LIKE', '%'.$keyword.'%')
            ->limit(10)
            ->get()
            ->map(fn (Song $song) => [
                'value' => $song->id,
                'label' => $song->title,
                'name' => $song->title,
            ])
        );
    }
}
