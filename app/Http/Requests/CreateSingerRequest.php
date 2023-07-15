<?php

namespace App\Http\Requests;

use App\Rules\UserUniqueForOrganisation;
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
        $this->merge(['onboarding_enabled' => ! $this->input('onboarding_disabled')]);
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
            'user_id' => [
                Rule::when(! empty($this->input('user_id')), [
                    Rule::exists('users', 'id'),
                    new UserUniqueForOrganisation,
                ]),
            ],
            'email' => [
                Rule::when(empty($this->input('user_id')), [
                    'required',
                    Rule::unique('users')
                        ->ignore($singer->user->id ?? ''),
                ]),
            ],
            'first_name' => ['exclude_without:email', 'required', 'max:127'],
            'last_name' => ['exclude_without:email', 'required', 'max:127'],
            'password' => ['exclude_without:email', 'sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
        ];

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
