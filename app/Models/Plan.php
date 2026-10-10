<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Plan implements Arrayable, JsonSerializable
{
    public string $name;
    public string $id;
    public ?string $short_description = null;
    public array $features = [];
    public array $options = [];

    public function __construct(string $name, string $id)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'short_description' => $this->short_description,
            'features' => $this->features,
            'options' => $this->options,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
