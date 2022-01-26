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

        $userRules = [
            'email' => [
                'required',
                Rule::unique('users')
                    ->ignore($singer->user->id ?? ''),
            ],
            'first_name' => ['required', 'max:127'],
            'last_name' => ['required', 'max:127'],
            'password' => ['sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
        ];

		if(config('features.user_search_in_create_singer')) {
		    $userRules = [
		        'user_id' => [
		            'sometimes',
                    Rule::exists('users', 'id'),
                ],
                'email' => [
                    'required_without:user_id',
                    Rule::unique('users')
                        ->ignore($singer->user->id ?? ''),
                ],
                'first_name' => ['exclude_without:email', 'required', 'max:127'],
                'last_name' => ['exclude_without:email', 'required', 'max:127'],
                'password' => ['exclude_without:email', 'sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
            ];
        }

		return array_merge($userRules, [
            'reason_for_joining' => ['max:255'],
            'referrer' => ['max:255'],
            'membership_details' => ['max:255'],
            'joined_at' => ['date', 'before_or_equal:today'],
            'onboarding_enabled' => ['boolean'],
            'voice_part_id' => [],
            'user_roles' => ['array', 'exists:roles,id'],
		]);
	}
}
