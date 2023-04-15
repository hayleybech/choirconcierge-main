<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Translation\PotentiallyTranslatedString;

class FileDoesntExist implements InvokableRule
{
    private string $path;
    private string $disk;

    public function __construct(string $disk, string $path) {
        $this->disk = $disk;
        $this->path = $path;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if(Storage::disk($this->disk)->exists($this->path.'/'.$value)) {
            $fail('A file with this name already exists.');
        }
    }
}
