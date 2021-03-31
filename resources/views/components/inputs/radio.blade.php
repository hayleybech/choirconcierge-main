@props(['label', 'id', 'name', 'value', 'checked' => false, 'inline' => false])
<div class="custom-control custom-radio" {{ $attributes->class(['custom-control-inline' => $inline]) }}>
    <input id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }} {{ $attributes->merge(['class' => 'custom-control-input']) }} type="radio">
    <label for="{{ $id }}" class="custom-control-label">{{ $label }}</label>
</div>