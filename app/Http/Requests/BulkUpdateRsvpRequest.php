<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\Rsvp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BulkUpdateRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Rsvp::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'singer_ids' => ['required', 'array', 'min:1'],
            'singer_ids.*' => ['required', 'integer', 'distinct', 'exists:memberships,id'],
            'response' => ['required', 'in:yes,maybe,no'],
        ];
    }

    public function messages(): array
    {
        return [
            'singer_ids.required' => 'Select at least one singer.',
            'singer_ids.min' => 'Select at least one singer.',
            'singer_ids.*.distinct' => 'Each singer may only be selected once.',
            'singer_ids.*.exists' => 'One or more selected singers do not exist.',
            'response.in' => 'The selected RSVP response is invalid.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Event $event */
            $event = $this->route('event');
            $singerIds = array_map('intval', $validator->validated()['singer_ids']);

            if ($event->relevant_memberships()->whereKey($singerIds)->count() !== count($singerIds)) {
                $validator->errors()->add('singer_ids', 'One or more singers are not eligible for this event.');
            }
        });
    }
}
