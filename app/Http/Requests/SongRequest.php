<?php

namespace App\Http\Requests;

use App\Models\Song;
use Illuminate\Foundation\Http\FormRequest;

class SongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Song $song
     * @return array<array>
     */
    public function rules(Song $song)
    {
        return [
            'title' => ['required', 'max:255'],
            'description' => ['nullable', 'max:5000'],
            'categories' => ['required', 'exists:song_categories,id'],
            'status' => ['required', 'exists:song_statuses,id'],
            'pitch_blown' => ['required'],
            'song' => [],
            'show_for_prospects' => ['boolean'],
            'send_notification' => ['boolean'],
        ];
    }
}
