@props(['label', 'id', 'name', 'options', 'selected' => ''])

<label for="{{ $id }}">{{ $label }}</label>
<select name="{{ $name }}" id="{{ $id }}" class="custom-select">
    @foreach($options as $key => $option)
        <option value="{{ $key }}" @if($key === $selected)selected @endif>{{ $option }}</option>
    @endforeach
</select>
<div class="valid-feedback">Looks good!</div>
<div class="invalid-feedback">Please choose an option.</div>