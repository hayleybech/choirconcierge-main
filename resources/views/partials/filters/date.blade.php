<datetime-range-input
    label=""
    input-name="date_range"
    start-name="filter_date[start]"
    end-name="filter_date[end]"
    :init-value="[new Date('{{ $filter->start_date }}'), new Date('{{ $filter->end_date }}')]"
    :small="true"
    type="date"
    :show-shortcuts="true"
/>
