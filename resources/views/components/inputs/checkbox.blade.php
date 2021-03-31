@props(['label', 'id', 'name', 'value', 'checked' => false, 'inline' => false])
<div class="custom-control custom-checkbox" {{ $attributes->class(['custom-control-inline' => $inline]) }}>
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }} {{ $attributes->merge(['class' => 'custom-control-input']) }}>
    <label class="custom-control-label" for="{{ $id }}">{{ $label }}</label>
</div>