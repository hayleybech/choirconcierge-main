<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
     * @param Folder $folder
     * @return array
     */
    public function rules(Folder $folder)
    {
        return [
            'title'             => 'required|max:255',
            'document_upload'   => 'required|file'
        ];
    }
}
