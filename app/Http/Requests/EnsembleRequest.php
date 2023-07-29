<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnsembleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): ?bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:127'],
	        'logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png', 'max:10240'],
        ];
    }
}
