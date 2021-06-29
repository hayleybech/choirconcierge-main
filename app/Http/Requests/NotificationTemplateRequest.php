<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationTemplateRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<array>
	 */
	public function rules(): array
	{
		return [
			'subject' => ['required', 'max:255'],
			'recipients' => ['required', 'max:255'],
			'body' => ['required', 'max:2000'],
			'delay' => ['required', 'max:255'],
		];
	}
}
