import React from "react";
import axios from 'axios';
import useRoute from '../../hooks/useRoute';
import AsyncSelect from 'react-select/async';

const StateSelect = ({ name, country, defaultValue, updateFn, multiple = false}) => {
	const { route } = useRoute();

	const load = (_inputValue) => axios
		.get(route('find.states', {country}))
		.then(response => response.data);

	return (
		<div className="mt-1">
			<AsyncSelect
				name={name}
				inputId={name}
				cacheOptions={country}
				loadOptions={load}
				isMulti={multiple}
				isClearable
				defaultValue={defaultValue}
				defaultOptions
				onChange={option => multiple ? updateFn(option.map(item => item?.value)) : updateFn(option?.value ?? '')}
				placeholder="Start typing..."
			/>
		</div>
	);
}

export default StateSelect;