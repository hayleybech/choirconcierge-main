import React from 'react';
import classNames from "../../classNames";
import {usePage} from "@inertiajs/inertia-react";
import GooglePlacesAutocomplete from 'react-google-places-autocomplete';

const LocationInput = ({ value, hasErrors, updateFn }) => {
    const { googleApiKey } = usePage().props;

    const customSelectStyles = { control: () => ({}) };

    return <GooglePlacesAutocomplete
        apiKey={googleApiKey}
        selectProps={{
            styles: customSelectStyles,
            components: { Control },
            value: value,
            onChange: newValue => updateFn({
                place_id: newValue.value.place_id,
                name: newValue.value.structured_formatting.main_text,
                icon: '',
                address: newValue.value.description,
            }),
            hasErrors
        }}
    />;
}

export default LocationInput;

const Control = ({ innerRef, innerProps, selectProps, children }) => (
    <div
        className={classNames('' +
            'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md border',
            selectProps.hasErrors
                ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500',
            'flex justify-between items-center',
        )}
        ref={innerRef}
        {...innerProps}
    >
        {children}
    </div>
);