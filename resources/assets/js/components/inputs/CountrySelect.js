import Select from "react-select";
import React from "react";

const CountrySelect = ({ name, options, defaultValue, updateFn, multiple = false}) => (
	<div className="mt-1">
		<Select
			name={name}
			inputId={name}
			cacheOptions
			options={options}
			isMulti={multiple}
			isClearable
			defaultValue={defaultValue}
			onChange={option => multiple ? updateFn(option.map(item => item?.value)) : updateFn(option?.value ?? '')}
			placeholder="Start typing..."
		/>
	</div>
);

export default CountrySelect;