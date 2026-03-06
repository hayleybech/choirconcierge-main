<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportSingerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()?->user()?->isSuperAdmin || auth()?->user()?->membership?->hasRole('Admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'import_csv' => [
                'required',
                'array',
            ],
            'import_csv.*' => [
                'required',
                'file',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $lineCount = count(@file($value->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []);
                    if ($lineCount <= 1) {
                        $fail('The uploaded file contains no data rows.');
                    }
                },
            ],
        ];
    }
}
