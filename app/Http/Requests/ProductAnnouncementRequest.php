<?php

namespace App\Http\Requests;

use App\Enums\EmailCategory;
use App\Enums\ProductAnnouncementAudience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->isSuperAdmin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'audience' => ['required', 'string', Rule::in(collect(ProductAnnouncementAudience::cases())->map(fn (ProductAnnouncementAudience $audience) => $audience->value)->all())],
            'category' => ['required', 'string', Rule::in(collect(EmailCategory::productCategories())->map(fn (EmailCategory $category) => $category->value)->all())],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'attachments' => ['sometimes', 'array'],
            'attachments.*' => ['file', 'max:5120'],
        ];
    }
}
