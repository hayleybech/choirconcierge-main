<?php

namespace App\Http\Requests;

use App\Models\Membership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEnrolmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ensemble_id' => [
                'required',
                'numeric',
                'exists:ensembles,id',
                Rule::unique('enrolments', 'ensemble_id')
                    ->where('membership_id', $this->singer->id),
            ],
            'voice_part_id' => ['required', 'numeric', 'exists:voice_parts,id'],
        ];
    }
}
