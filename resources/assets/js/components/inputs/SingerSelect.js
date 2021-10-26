import React from 'react';
import {components} from "react-select";
import AsyncSelect from 'react-select/async';
import axios from 'axios';

const SingerSelect = ({ defaultValue, updateFn, multiple = false }) => {

    const load = (inputValue) => axios
        .get(route('find.singers', { q: inputValue }))
        .then(response => response.data);

    return (
        <div className="mt-1">
            <AsyncSelect
                cacheOptions
                defaultOptions={[]}
                loadOptions={load}
                components={{ SingleValue }}
                isMulti={multiple}
                defaultValue={defaultValue}
                onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
            />
        </div>
    );
}

export default SingerSelect;

const SingleValue = ({ data: { name }, children, ...props }) => (
    <components.SingleValue {...props}>{name}</components.SingleValue>
);