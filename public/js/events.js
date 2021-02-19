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
    "showDropdowns": true,
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
        this.element.siblings('.call-time-hidden').val( start.format(DATE_FORMAT_RAW) );
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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// LOCATION

function initMap() {
    let el_location = document.querySelector('.location-input-wrapper');
    let el_input = el_location.querySelector('.location-input');
    let el_place_id_raw = el_location.querySelector('.location-place-id');
    let el_icon_raw = el_location.querySelector('.location-icon');
    let el_name_raw = el_location.querySelector('.location-name');
    let el_address_raw = el_location.querySelector('.location-address');
    let el_place = el_location.querySelector('.location-place');
    let el_icon = el_place.querySelector('.place-icon');
    let el_name = el_place.querySelector('.place-name');
    let el_address = el_place.querySelector('.place-address');

    let autocomplete = new google.maps.places.Autocomplete(el_input);

    // Set the data fields to return when the user selects a place.
    autocomplete.setFields(
        ['place_id', 'address_components', 'icon', 'name']);

    autocomplete.addListener('place_changed', function() {
        let place = autocomplete.getPlace();

        if ( ! place.place_id ) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        // Format the address
        let address = '';
        let address_parts = [];
        if (place.address_components) {
            place.address_components.forEach(function(part){
                if(part.short_name) {
                    address_parts.push(part.short_name);
                }
            });

            address = address_parts.join(', ');
        }

        // Output the place details
        // Hidden Fields
        el_place_id_raw.value = place.place_id;
        el_icon_raw.value = place.icon;
        el_name_raw.value = place.name;
        el_address_raw.value = address;
        // Display
        el_icon.style.backgroundImage = "url('"+place.icon+"')";
        el_name.textContent = place.name;
        el_address.textContent = address;
    });

    // Clear the location when the input is cleared
    el_input.addEventListener('change', function() {
        // Clear the place details
        // Hidden Fields
        el_place_id_raw.value = '';
        el_icon_raw.value = '';
        el_name_raw.value = '';
        el_address_raw.value = '';
        // Display
        el_icon.style.backgroundImage = 'none';
        el_name.textContent = '';
        el_address.textContent = '';
    });
}