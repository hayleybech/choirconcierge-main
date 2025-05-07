import React from 'react';
import AsyncSelect from 'react-select/async';
import axios from 'axios';
import useRoute from "../../hooks/useRoute";

const StateSelect = ({ countryId, defaultValue, updateFn, multiple = false }) => {
    const { route } = useRoute();

    const load = (keyword) => axios
        .get(route('find.states', {keyword, countryId}))
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
                placeholder="Start typing..."
            />
        </div>
    );
}

export default StateSelect;
