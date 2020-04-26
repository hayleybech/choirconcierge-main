<?php

namespace App\Http\Requests;

use App\Models\UserGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserGroupRequest extends FormRequest
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
     * @param UserGroup $group
     * @return array
     */
    public function rules(UserGroup $group)
    {
        return [
            'title'             => 'required|max:255',
            'slug'              => [
                'required',
                Rule::unique('user_groups')->ignore($this->group->id ?? ''),
                'max:255'
            ],
            'list_type'         => 'required',
            'recipient_roles'  => '',
            'recipient_voice_parts' => '',
            'recipient_users'  => '',
            'recipient_singer_categories'  => '',
        ];
    }
}
