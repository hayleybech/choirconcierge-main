<?php

namespace App\Http\Requests;

use App\Models\Singer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SingerRequest extends FormRequest
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
		$this->whenHas('onboarding_disabled', fn() => $this->merge(['onboarding_enabled' => false]));
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<array>
	 */
	public function rules()
	{
		$singer = $this->route('singer');
		return [
			'first_name' => ['required', 'max:127'],
			'last_name' => ['required', 'max:127'],
			'email' => [
				'required',
				Rule::unique('singers')
					->where('tenant_id', tenant('id'))
					->ignore($singer->id ?? ''),
			],
			'joined_at' => ['date', 'before_or_equal:today'],
			'onboarding_enabled' => ['boolean'],
			'voice_part_id' => [],
			'user_roles' => ['array', 'exists:roles,id'],
			'password' => ['sometimes', 'nullable', 'min:8', 'max:255', 'confirmed'],
			'avatar' => ['file', 'mimetypes:image/jpeg,image/png', 'max:10240'],
		];
	}
}
