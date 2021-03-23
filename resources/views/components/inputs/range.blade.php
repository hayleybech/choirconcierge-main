@props(['label', 'id', 'name', 'helpText' => '', 'small' => false, 'labelClass' => '', 'min', 'max', 'minDesc', 'maxDesc'])
<label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }}</label>

<div class="d-flex align-items-start">
    <div class="mr-4" class="text-center">
        {{ $minDesc ?? $min ?? '' }}
    </div>

    <input id="{{ $id }}" name="{{ $name }}" min="{{ $min }}" max="{{ $max }}" {{ $attributes->class(['custom-range'])->merge(['value' => '']) }} type="range">

    <div class="ml-4" class="text-center">
        {{ $maxDesc ?? $max ?? '' }}
    </div>
</div>

@if( $helpText )
    <p>
        <small class="text-muted">{{ $helpText }}</small>
    </p>
@endif