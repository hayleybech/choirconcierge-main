<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'can_vote_multiple' => ['sometimes', 'boolean'],
            'close_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'is_closed' => ['sometimes', 'boolean'],
            'options' => ['required', 'array', 'min:1'],
            'options.*' => ['required', 'string', 'max:255'],
            'send_notification' => ['sometimes', 'boolean'],
        ];
    }
}
