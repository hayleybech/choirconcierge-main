import Select from 'react-select';
import React from 'react';

import statesUsa from "states-us";

const countryStateOptions = {
	AU: [
		{ value: 'ACT', label: 'ACT' },
		{ value: 'NSW', label: 'NSW' },
		{ value: 'NT', label: 'NT' },
		{ value: 'QLD', label: 'QLD' },
		{ value: 'SA', label: 'SA' },
		{ value: 'TAS', label: 'TAS' },
		{ value: 'VIC', label: 'VIC' },
		{ value: 'WA', label: 'WA' },
	],
	CA: [
		{ value: 'AB', label: 'Alberta' },
		{ value: 'BC', label: 'British Columbia' },
		{ value: 'MB', label: 'Manitoba' },
		{ value: 'NB', label: 'New Brunswick' },
		{ value: 'NL', label: 'Newfoundland and Labrador' },
		{ value: 'NT', label: 'Northwest Territories' },
		{ value: 'NS', label: 'Nova Scotia' },
		{ value: 'NU', label: 'Nunavut' },
		{ value: 'ON', label: 'Ontario' },
		{ value: 'PE', label: 'Prince Edward Island' },
		{ value: 'QC', label: 'Quebec' },
		{ value: 'SK', label: 'Saskatchewan' },
		{ value: 'YT', label: 'Yukon Territory' },
	],
	US: statesUsa.map(state => ({ value: state.abbreviation, label: state.name })),
};

const StateSelect = ({ country, defaultValue, updateFn, ...rest }) => {
	console.log({statesUsa});
	if (!country in countryStateOptions) {
		return null;
	}

	return (
		<Select
			cacheOptions
			options={countryStateOptions[country]}
			defaultValue={countryStateOptions[country]?.filter((state => state.value === defaultValue))}
			onChange={option => updateFn(option.value)}
			{...rest}
			key={country}
		/>
	);
};

export default StateSelect;
