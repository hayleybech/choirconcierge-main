<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'dob'                   => 'date|before:today',
            'phone'                 => 'max:255',
            'ice_name'              => 'max:255',
            'ice_phone'             => 'max:255',
            'reason_for_joining'    => 'max:255',
            'referrer'              => 'max:255',
            'profession'            => 'max:255',
            'skills'                => 'max:255',
        ];
    }
}
