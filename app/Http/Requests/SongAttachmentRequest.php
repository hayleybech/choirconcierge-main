<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SongAttachmentRequest extends FormRequest
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
     * @return array<array>
     */
    public function rules()
    {
        return [
            'type' => ['required', 'in:sheet-music,full-mix-demo,learning-tracks,other'],
            'attachment_uploads' => ['required', 'array'],
            'attachment_uploads.*' => ['required', 'file'],
        ];
    }
}
