<div class="input-group input-group-sm mb-2 mr-2">
    <div class="input-group-prepend">
        @php
            $label_class = $filter->isDefault() ? 'bg-light' : 'border-primary bg-primary text-white';
        @endphp
        <label for="{{ $filter->name }} " class="input-group-text {{$label_class}}">{{ $filter->label }}</label>
    </div>
    @php
        $field_class = $filter->isDefault() ? '' : 'border-primary';
        echo Form::select($filter->name,
            $filter->options,
            $filter->current_option,
            ['class' => 'custom-select form-control-sm ' . $field_class]
        );
    @endphp
</div>