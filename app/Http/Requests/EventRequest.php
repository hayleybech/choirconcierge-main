<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $this->merge([
            'is_repeating' => $this->has('is_repeating'),
            'send_notification' => $this->has('send_notification'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string>
     */
    public function rules()
    {
        return [
            'title'             => 'required|max:255',
            'type'              => 'required|exists:event_types,id',
            'call_time'         => 'required|date_format:Y-m-d H:i:s|before:start_date',
            'start_date'        => 'required|date_format:Y-m-d H:i:s',
            'end_date'          => 'required|date_format:Y-m-d H:i:s|after:start_date',
            'location_place_id' => 'nullable',
            'location_icon'     => 'nullable',
            'location_name'     => 'nullable',
            'location_address'  => 'nullable',
            'description'       => 'nullable|max:5000',

            'is_repeating'          => 'boolean',
            'repeat_frequency_unit' => 'exclude_unless:is_repeating,true|required',
            'repeat_until'          => 'exclude_unless:is_repeating,true|required|date_format:Y-m-d H:i:s|after:start_date',

            'send_notification' => 'boolean',
        ];
    }
}
