<?php

namespace App\Http\Requests;

use App\Enums\EmailCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailPreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categories = EmailCategory::cases();
        $canReceiveAttendanceReports = $this->user()->canReceiveAttendanceReports();
        $keys = collect($categories)->map(fn (EmailCategory $category) => $category->value)->join(',');
        $rules = [
            'email_preferences' => ['required', 'array:' . $keys],
        ];

        foreach ($categories as $category) {
            if ($category === EmailCategory::ATTENDANCE_REPORTS && ! $canReceiveAttendanceReports) {
                $rules['email_preferences.' . $category->value] = ['sometimes', 'boolean'];
            } else {
                $rules['email_preferences.' . $category->value] = $category->isMandatory()
                    ? ['required', 'accepted']
                    : ['required', 'boolean'];
            }
        }

        return $rules;
    }
}
