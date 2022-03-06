<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditSingerRequest extends FormRequest
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
        $this->merge(['onboarding_enabled' => ! $this->input('onboarding_disabled')]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<array>
	 */
	public function rules()
	{
		return [
            'reason_for_joining' => ['max:255'],
            'referrer' => ['max:255'],
            'membership_details' => ['max:255'],
            'joined_at' => ['date', 'before_or_equal:today'],
            'onboarding_enabled' => ['boolean'],
			'voice_part_id' => ['numeric', 'exists:voice_parts,id'],
			'user_roles' => auth()->user()->singer->hasAbility('roles_create')
                ? ['array', 'exists:roles,id']
                : ['prohibited'],
		];
	}
}
