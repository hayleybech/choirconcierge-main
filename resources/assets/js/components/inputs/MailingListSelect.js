import React from 'react';
import {components} from "react-select";
import Select from "react-select";

const MailingListSelect = ({ options, defaultValue, updateFn, multiple = false }) => (
    <div className="mt-1">
        <Select
            cacheOptions
            options={options}
            components={{ SingleValue, MultiValueLabel, Option }}
            isMulti={multiple}
            defaultValue={defaultValue}
            onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
        />
    </div>
);

export default MailingListSelect;

const SingleValue = ({ data: { label, email }, children, ...props }) => (
    <components.SingleValue {...props}>
        <span className="text-gray-700 inline-block ml-2">{label}</span>
        <span className="ml-2 text-gray-500 text-sm">{email}</span>
    </components.SingleValue>
);

const MultiValueLabel =({ data: { label, email }, children, ...props }) => (
    <components.MultiValueLabel {...props}>
        <span className="text-gray-700 inline-block ml-2">{label}</span>
        <span className="ml-2 text-gray-500 text-sm">{email}</span>
    </components.MultiValueLabel>
);

const Option = ({ data: { label, email }, children, ...props }) => (
    <components.Option {...props}>
        <div className="text-gray-700">{label}</div>
        <div className="mt-2 text-gray-500 text-sm">{email}</div>
    </components.Option>
);