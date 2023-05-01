import React from 'react';
import classNames from "../../classNames";
import {usePage} from "@inertiajs/inertia-react";
import GooglePlacesAutocomplete from 'react-google-places-autocomplete';
import { components } from 'react-select';
import Icon from "../Icon";

const LocationInput = ({ value, hasErrors, updateFn }) => {
    const { googleApiKey } = usePage().props;

    const customSelectStyles = { control: () => ({}) };

    const clear = () => {
        if(! value.value.place_id) {
            return;
        }
        updateFn({
            address: null,
            place_id: null,
            name: null,
            icon: '',
        });
    };

    const saveManualAddress = (newValue) => {
        if(! newValue) {
            return false;
        }
        updateFn({
            address: newValue,
            place_id: null,
            name: null,
            icon: '',
        })
    };

    const savePlace = (newValue) => {
        if(! newValue?.value?.place_id) {
            return;
        }
        updateFn({
            place_id: newValue.value.place_id,
            name: newValue.value.structured_formatting.main_text,
            icon: '',
            address: newValue.value.description,
        });
    }

    return <GooglePlacesAutocomplete
        apiKey={googleApiKey}
        selectProps={{
            styles: customSelectStyles,
            components: { Control, DropdownIndicator, SingleValue },
            value: value,
            onFocus: clear,
            onInputChange: saveManualAddress,
            onChange: savePlace,
            escapeClearsValue: false,
            hasErrors
        }}
    />;
}

export default LocationInput;

const Control = ({ innerRef, innerProps, selectProps, children }) => (
    <div
        className={classNames('' +
            'shadow-sm focus:outline-none block w-full sm:text-sm rounded-md border bg-white',
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

const DropdownIndicator = (props) => (
    <components.DropdownIndicator {...props}>
        {props.selectProps.menuIsOpen
         ? <div className="text-purple-800 hover:text-purple-600 cursor-pointer">Use manually-entered address</div>
         : <Icon icon="chevron-down" className="cursor-pointer" />
        }
    </components.DropdownIndicator>
);

const SingleValue = ({ children, ...props }) => (
    <components.SingleValue {...props}>
        {props.selectProps?.value?.value?.place_id && <Icon icon="map-marker-check" type="regular" mr />}
        {children}
    </components.SingleValue>
);