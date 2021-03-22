@props(['type' => 'text', 'label', 'id', 'name', 'value' => '', 'helpText' => ''])
<label for="{{ $id }}">{{ $label }}</label>
<input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" class="form-control">
@if( $helpText )
<p>
    <small class="text-muted">{{ $helpText }}</small>
</p>
@endif