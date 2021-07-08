<?php

namespace App\Http\Requests;

use App\Models\Singer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SingerRequest extends FormRequest
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
		$this->whenHas('onboarding_disabled', fn() => $this->merge(['onboarding_enabled' => false]));
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
			'user_roles' => ['array', 'exists:roles,id'],
		];
	}
}
