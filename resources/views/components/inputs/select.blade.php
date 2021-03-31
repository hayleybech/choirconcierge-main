@props(['label', 'id', 'name', 'options', 'selected' => '', 'small' => false])

<label for="{{ $id }}">{{ $label }}</label>
<select name="{{ $name }}" id="{{ $id }}" {{ $attributes->class(['custom-select', 'custom-select-sm' => $small]) }}>
@if(is_array( collect($options)->first() ))
    @foreach($options as $label => $options_inner)
        <optgroup label="{{ $label }}">
            @foreach($options_inner as $key => $option)
                <option value="{{ $key }}" @if($key === $selected)selected @endif>{{ $option }}</option>
            @endforeach
        </optgroup>
    @endforeach
@else
    @foreach($options as $key => $option)
        <option value="{{ $key }}" @if($key === $selected)selected @endif>{{ $option }}</option>
    @endforeach
@endif
</select>
<div class="valid-feedback">Looks good!</div>
<div class="invalid-feedback">Please choose an option.</div>