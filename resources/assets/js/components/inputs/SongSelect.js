import React from 'react';
import AsyncSelect from 'react-select/async';
import axios from 'axios';

const SongSelect = ({ defaultValue, updateFn, multiple = false }) => {

    const load = (inputValue) => axios
        .get(route('find.songs', inputValue))
        .then(response => response.data);

    return (
        <div className="mt-1">
            <AsyncSelect
                cacheOptions
                defaultOptions={[]}
                loadOptions={load}
                isMulti={multiple}
                defaultValue={defaultValue}
                onChange={option => multiple ? updateFn(option.map(item => item.value)) : updateFn(option.value)}
            />
        </div>
    );
}

export default SongSelect;
