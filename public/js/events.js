
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