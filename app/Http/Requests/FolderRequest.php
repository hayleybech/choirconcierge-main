<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends FormRequest
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
     * @return array<array>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'max:255'],
            'ensembles' => ['nullable', 'array'],
            'ensembles.*' => ['exists:ensembles,id'],
            'viewer_users' => ['nullable', 'array'],
            'viewer_users.*' => ['exists:users,id'],
            'viewer_roles' => ['nullable', 'array'],
            'viewer_roles.*' => ['exists:roles,id'],
            'viewer_voice_parts' => ['nullable', 'array'],
            'viewer_voice_parts.*' => ['exists:voice_parts,id'],
            'viewer_singer_categories' => ['nullable', 'array'],
            'viewer_singer_categories.*' => ['exists:singer_categories,id'],
            'editor_users' => ['nullable', 'array'],
            'editor_users.*' => ['exists:users,id'],
            'editor_roles' => ['nullable', 'array'],
            'editor_roles.*' => ['exists:roles,id'],
            'editor_voice_parts' => ['nullable', 'array'],
            'editor_voice_parts.*' => ['exists:voice_parts,id'],
            'editor_singer_categories' => ['nullable', 'array'],
            'editor_singer_categories.*' => ['exists:singer_categories,id'],
        ];
    }
}
