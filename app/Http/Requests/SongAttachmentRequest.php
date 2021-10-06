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
			//'title'               => ['required', 'max:255'],
			'category' => ['required', 'exists:song_attachment_categories,id'],
            'attachment_uploads' => ['required', 'array'],
			'attachment_uploads.*' => ['required', 'file'],
		];
	}
}
