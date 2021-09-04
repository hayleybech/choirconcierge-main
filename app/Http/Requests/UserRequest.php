<?php

namespace App\Http\Requests;

use App\Models\Singer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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

	public function prepareForValidation()
	{
        if(config('features.rebuild')) {
            $this->merge(['onboarding_enabled' => ! $this->input('onboarding_disabled')]);
        } else {
            $this->whenHas('onboarding_disabled', fn() => $this->merge(['onboarding_enabled' => false]));
        }
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<array>
	 */
	public function rules()
	{
		$singer = $this->route('singer');
		return [
		    // User
            'first_name' => ['required', 'max:127'],
            'last_name' => ['required', 'max:127'],
            'email' => [
                'required',
                Rule::unique('users')
                    ->where('tenant_id', tenant('id'))
                    ->ignore($singer->user->id ?? ''),
            ],
            'password' => ['sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
            'avatar' => ['file', 'mimetypes:image/jpeg,image/png', 'max:10240'],
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

            // Singer
            'reason_for_joining' => ['max:255'],
            'referrer' => ['max:255'],
            'membership_details' => ['max:255'],
            'joined_at' => ['date', 'before_or_equal:today'],
            'onboarding_enabled' => ['boolean'],
            'voice_part_id' => [],
            'user_roles' => ['array', 'exists:roles,id'],
		];
	}
}
