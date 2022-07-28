import React from 'react';
import Select from "react-select";

const TimezoneSelect = ({ options, defaultValue, updateFn, multiple = false }) => (
    <div className="mt-1">
        <Select
            defaultInputValue={defaultValue}
            cacheOptions
            options={options}
            isMulti={multiple}
            defaultValue={defaultValue}
            onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
        />
    </div>
);

export default TimezoneSelect;