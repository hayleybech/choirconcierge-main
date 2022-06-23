<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order' => ['required', 'integer', 'max:255'],
            'activity_type_id' => ['required', 'exists:activity_types,id'],
            'song' => ['nullable', 'exists:songs'],
            'singer' => ['nullable', 'exists:singers'],
            'notes' => ['nullable', 'max:255'],
            'duration' => ['nullable', 'integer', 'max:255'],
        ];
    }
}
