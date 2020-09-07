<div class="input-group">
    <div class="input-group-prepend">
        <span class="input-group-text"><i class="fa fa-fw fa-calendar-day"></i></span>
    </div>

    {{ Form::text('date_range', $filter->start_date->format('M d, Y H:i') . ' - ' . $filter->end_date->format('M d, Y H:i'), ['class' => 'form-control form-control-sm events-date-range-picker']) }}
    {{ Form::hidden('filter_date[start]', $filter->start_date, ['class' => 'start-date-hidden']) }}
    {{ Form::hidden('filter_date[end]', $filter->end_date, ['class' => 'end-date-hidden']) }}
</div>

@push('scripts-footer-bottom')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script>
        const DATE_FORMAT_RAW = 'YYYY-MM-DD HH:mm:ss';
        const DATE_FORMAT_DISPLAY = 'MMMM D, YYYY hh:mm A';

        const DATE_CONFIG = {
            "showISOWeekNumbers": true,
            "showDropdowns": true,
            "timePicker": false,
            "timePickerIncrement": 15,
            "locale": {
                "format": DATE_FORMAT_DISPLAY,
                "firstDay": 1
            },
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true
        };
        const $el_range = $('.events-date-range-picker');
        const $el_start_raw = $('.start-date-hidden');
        const $el_end_raw   = $('.end-date-hidden');

        // Events - Event Date Range
        $el_range.daterangepicker({
                ...DATE_CONFIG

            },
            function(start, end, label) {
                // Save the raw dates we need to hidden fields
                $el_start_raw.val( start.format(DATE_FORMAT_RAW) );
                $el_end_raw.val( end.format(DATE_FORMAT_RAW) );
            }
        );
    </script>
@endpush