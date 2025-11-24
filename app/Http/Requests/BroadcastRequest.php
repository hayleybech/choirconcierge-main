<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BroadcastRequest extends FormRequest
{
    private const MAX_FILE_SIZE_MB = 5;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'list' => ['required', 'exists:user_groups,id'],
            'subject' => ['required', 'max:255'],
            'body' => ['required', 'max:5000'],
            'attachments.*' => ['sometimes', 'file', 'max:' . self::MAX_FILE_SIZE_MB * 1024],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'attachments.*.max' => 'Attachment #:position must be no more than ' . self::MAX_FILE_SIZE_MB . 'mb.',
        ];
    }
}
