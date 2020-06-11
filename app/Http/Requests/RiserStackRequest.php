<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiserStackRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title'     => 'required|max:255',
            'rows'      => 'required|integer|min:1|max:255',
            'columns'   => 'required|integer|min:1|max:255',
            'front_row_length'  => 'required|integer|min:1|max:255',
            'singer_positions'  => 'required|json',
        ];
    }
}
