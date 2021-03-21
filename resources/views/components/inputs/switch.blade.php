@props(['label', 'name', 'value', 'checked' => false, 'helpText' => ''])
<div class="custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }}>
    <label class="custom-control-label" for="{{ $name }}">{{ $label }}</label>
</div>
@if( $helpText )
<p>
    <small class="text-muted">{{ $helpText }}</small>
</p>
@endif