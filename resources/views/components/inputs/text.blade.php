@props(['label', 'id', 'name', 'helpText' => '', 'small' => false, 'labelClass' => ''])
<label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }}</label>
<input id="{{ $id }}" name="{{ $name }}" {{ $attributes->class(['form-control', 'form-control-sm' => $small])->merge(['value' => '', 'type' => 'text']) }}>
@if( $helpText )
<p>
    <small class="text-muted">{{ $helpText }}</small>
</p>
@endif