<?php

namespace App\Http\Requests;

use App\Rules\UserUniqueForOrganisation;
use App\Enums\SingerStatus;
use App\Models\Ensemble;
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
        $this->merge([
            'onboarding_enabled' => ! $this->input('onboarding_disabled'),
            'status' => $this->input('status', SingerStatus::PROSPECTS->value),
            'email' => str($this->email)->trim()->lower()->toString(),
        ]);
    }

    /**
     * Default the enrolments to the single ensemble when none were submitted,
     * now that the submitted data has passed validation.
     */
    public function passedValidation(): void
    {
        $enrolments = $this->validated('enrolments', []);

        if (empty($enrolments) && Ensemble::count() === 1) {
            $enrolments = [[
                'ensemble_id' => Ensemble::first()->id,
                'voice_part_id' => null,
            ]];
        }

        $this->merge(['enrolments' => $enrolments]);
    }

    /**
     * Get the enrolments that should be created for this singer.
     *
     * @return array<array{ensemble_id: int, voice_part_id: ?int}>
     */
    public function enrolments(): array
    {
        return $this->input('enrolments', []);
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
                'nullable',
                Rule::when(! empty($this->input('user_id')), [
                    Rule::exists('users', 'id'),
                    new UserUniqueForOrganisation,
                ]),
            ],
            'email' => [
                Rule::when(empty($this->input('user_id')), [
                    'required',
                    'email',
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
            'onboarding_enabled' => ['boolean'],
            'status' => ['required', Rule::enum(SingerStatus::class)],
            'user_roles' => ['array', 'exists:roles,id'],
            'enrolments' => ['array'],
            'enrolments.*.ensemble_id' => ['required', 'numeric', 'exists:ensembles,id'],
            'enrolments.*.voice_part_id' => ['nullable', 'numeric', 'exists:voice_parts,id'],
        ]);
    }
}
