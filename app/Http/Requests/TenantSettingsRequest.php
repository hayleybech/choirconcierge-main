<?php

namespace App\Http\Requests;

use DateTimeZone;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', tenant()) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tab' => ['required', 'string', Rule::in(['organisation', 'billing'])],
        ];

        if ($this->input('tab') === 'billing') {
            return [
                ...$rules,
                'billing_user' => ['present', 'nullable', 'integer', 'exists:memberships,id'],
            ];
        }

        if ($this->input('tab') !== 'organisation') {
            return $rules;
        }

        return [
            ...$rules,
            'name' => ['required', 'max:127'],
            'logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:10240'],
            'primary_domain' => [
                'required',
                'max:127',
                'alpha_dash',
                Rule::unique('domains', 'domain')
                    ->where(fn (Builder $query) => $query->whereNot('tenant_id', tenant()->id)),
            ],
            'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
        ];
    }
}
