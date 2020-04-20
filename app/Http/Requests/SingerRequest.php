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

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Singer $singer
     * @return array
     */
    public function rules(Singer $singer)
    {
        return [
            'name'	=> 'required',
            'email'	=> [
                'required',
                Rule::unique('singers')->ignore($singer->id ?? ''),
            ],
            'onboarding_enabled'    => 'boolean',
            'user_roles' => [
                'array',
                'exists:roles,id',
            ],
            'password' => 'confirmed|nullable',
        ];
    }
}
