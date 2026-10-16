<?php

namespace App\Http\Requests;

use App\Models\Rsvp;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSingerRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Rsvp::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'response' => ['required', 'in:yes,maybe,no'],
        ];
    }

    public function messages(): array
    {
        return [
            'response.in' => 'The selected RSVP response is invalid.',
        ];
    }
}
