<?php

namespace App\Http\Requests;

use App\Models\Singer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSingerRequest extends FormRequest
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
