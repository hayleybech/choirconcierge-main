/*
 * Event
 *
 * Date Range
 * - UI is a date-time range picker
 * - Raw data is 2 separate hidden fields
 *
 * Call Time
 * - UI is a time picker select control
 * - Raw data uses a time AND date
 * - Date is ALWAYS set from event start date
 */

// Event constants
const DATE_FORMAT_RAW = 'YYYY-MM-DD HH:mm:ss';
const DATE_FORMAT_DISPLAY = 'MMMM D, YYYY hh:mm A';

const DATE_CONFIG = {
    "showISOWeekNumbers": true,
    "timePicker": true,
    "timePickerIncrement": 15,
    "locale": {
        "format": DATE_FORMAT_DISPLAY,
        "firstDay": 1
    }
};

// Event elements
//const $el_call_time_hr = $('.call-time-hr');
//const $el_call_time_min = $('.call-time-min');
//const $el_call_time_date = $('.call-time-date');
const $el_call_time = $('.events-single-date-picker');
const $el_call_time_raw = $('.call-time-hidden');
const $el_range = $('.events-date-range-picker');
const $el_start_raw = $('.start-date-hidden');
const $el_end_raw   = $('.end-date-hidden');

// Events - Call Time (Single Date Picker)
$el_call_time.daterangepicker({
        ...DATE_CONFIG,
        'singleDatePicker': true
    },
    function(start, end, label){
        console.log(start.format(DATE_FORMAT_RAW));
        $el_call_time_raw.val( start.format(DATE_FORMAT_RAW) );
    }
);

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

// Call Time (Time selects)
/*
$el_call_time_hr.add($el_call_time_min).on('change', function(){
    // Make time string from inputs
    call_time_time_raw = $el_call_time_hr.val() + ':' + $el_call_time_min.val() + ':00';

    // Store time string in the hidden field
    $el_call_time_raw.val(call_time_date_raw + ' ' + call_time_time_raw);
});*/