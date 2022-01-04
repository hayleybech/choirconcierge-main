<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<array>
	 */
	public function rules(): array
	{
		return [
            'first_name' => ['required', 'max:127'],
            'last_name' => ['required', 'max:127'],
            'pronouns' => ['max:127'],
            'email' => [
                'required',
                Rule::unique('users')
                    ->where('tenant_id', tenant('id'))
                    ->ignore(auth()->user()->id),
            ],
            'password' => ['sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
            'avatar' => ['sometimes', 'nullable', 'file', 'mimetypes:image/jpeg,image/png', 'max:10240'],
            'dob' => ['date', 'before:today'],
            'phone' => ['max:255'],
            'ice_name' => ['max:255'],
            'ice_phone' => ['max:255'],
            'address_street_1' => ['max:255'],
            'address_street_2' => ['max:255'],
            'address_suburb' => ['max:255'],
            'address_state' => ['max:3'],
            'address_postcode' => ['max:4'],
            'profession' => ['max:255'],
            'skills' => ['max:255'],
            'height' => ['nullable', 'numeric', 'between:0,300'],
            'bha_id' => ['nullable', 'numeric'],
		];
	}
}
