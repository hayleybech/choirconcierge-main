@props(['label', 'id', 'name', 'required' => false, 'multiple' => false])
<label for="{{ $id }}">{{ $label }}</label>

<div class="custom-file custom-file-sm">
    <input id="{{ $id }}" name="{{ $name }}" class="custom-file-input @error($name) is-invalid @enderror" type="file" @if($multiple)multiple @endif @if($required)required @endif>
    <div class="custom-file-label form-control-sm">Choose file</div>

    <div class="valid-feedback">Looks good!</div>
    <div class="invalid-feedback">Please upload a file.</div>
</div>