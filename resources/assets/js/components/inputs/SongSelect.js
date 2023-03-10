import React from 'react';
import AsyncSelect from 'react-select/async';
import axios from 'axios';
import useRoute from "../../hooks/useRoute";

const SongSelect = ({ defaultValue, updateFn, multiple = false }) => {
    const { route } = useRoute();

    const load = (inputValue) => axios
        .get(route('find.songs', {keyword: inputValue}))
        .then(response => response.data);

    return (
        <div className="mt-1">
            <AsyncSelect
                cacheOptions
                defaultOptions={[]}
                loadOptions={load}
                isMulti={multiple}
                defaultValue={defaultValue}
                onChange={option => multiple ? updateFn(option.map(item => item?.value)) : updateFn(option?.value)}
                isClearable
            />
        </div>
    );
}

export default SongSelect;
