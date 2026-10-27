<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $optionIdRules = ['nullable', 'integer'];

        if ($poll = $this->route('poll')) {
            $optionIdRules[] = Rule::exists('poll_options', 'id')->where('poll_id', $poll->id);
        } else {
            $optionIdRules[] = 'prohibited';
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'can_vote_multiple' => ['sometimes', 'boolean'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'close_at' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'is_closed' => ['sometimes', 'boolean'],
            'options' => ['required', 'array', 'min:1'],
            'options.*' => ['required', 'string', 'max:255'],
            'option_ids' => ['sometimes', 'array'],
            'option_ids.*' => $optionIdRules,
            'ensemble_ids' => ['nullable', 'array'],
            'ensemble_ids.*' => ['exists:ensembles,id'],
        ];
    }
}
