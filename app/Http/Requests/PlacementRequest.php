<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlacementRequest extends FormRequest
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
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'experience'         => 'max:255',
            'instruments'        => 'max:255',
            'skill_pitch'        => 'nullable|sometimes|min:1|max:5',
            'skill_harmony'      => 'nullable|sometimes|min:1|max:5',
            'skill_performance'  => 'nullable|sometimes|min:1|max:5',
            'skill_sightreading' => 'nullable|sometimes|min:1|max:5',
            'voice_tone'         => 'nullable|sometimes|min:1|max:3',
            'voice_part_id'      => '',
        ];
    }
}
