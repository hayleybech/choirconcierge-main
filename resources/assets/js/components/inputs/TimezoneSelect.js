import React from 'react';
import Select from "react-select";
import reactSelectStyles from './reactSelectStyles';

const TimezoneSelect = ({ options, defaultValue, updateFn, multiple = false }) => (
    <div className="mt-1">
        <Select
            defaultInputValue={defaultValue}
            cacheOptions
            options={options}
            isMulti={multiple}
            defaultValue={defaultValue}
            onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
            styles={reactSelectStyles}
        />
    </div>
);

export default TimezoneSelect;