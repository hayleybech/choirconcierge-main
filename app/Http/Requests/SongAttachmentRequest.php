<?php

namespace App\Http\Requests;

use App\Rules\FileDoesntExist;
use App\Rules\Filename;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

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
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $attachment = $this->route('attachment');

            if ($attachment->type === 'youtube') {
                return [
                    'url' => ['required', 'url', 'max:255'],
                    'title' => ['required', 'string', 'max:255'],
                ];
            }

            return [
                'filename' => [
                    'required',
                    'max:255',
                    new Filename,
                    new FileDoesntExist('tenant', $attachment->getPathSong()),
                ]
            ];
        }

        return [
            'type' => ['required', 'in:sheet-music,full-mix-demo,learning-tracks,youtube,other'],
            'attachment_uploads' => [Rule::excludeIf(fn () => $this->isVideo()), 'required', 'array'],
            'attachment_uploads.*' => [Rule::excludeIf(fn () => $this->isVideo()), 'required', 'file'],
            'url' => [Rule::excludeIf(fn () => !$this->isVideo()), 'required', 'url', 'max:255'],
            'title' => ['max:255', Rule::requiredIf(fn () => $this->isVideo())],
        ];
    }

    private function isVideo(): bool {
        return 'youtube' === $this->input('type');
    }
}
