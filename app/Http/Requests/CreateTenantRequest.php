<?php

namespace App\Http\Requests;

use DateTimeZone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:127'],
            'logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:10240'],
            'primary_domain' => ['required', 'max:127', 'alpha_dash', Rule::unique('domains', 'domain', '')],
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],

            'ensemble_name' => ['required', 'max:127'],
            'ensemble_logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:10240'],
        ];
    }
}

