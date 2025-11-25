import Select from 'react-select';
import React from 'react';
import { countries } from 'countries-list';

const countryOptions = Object.entries(countries).map(([code, country]) => ({
	value: code,
	label: country.name,
}));

const CountrySelect = ({ defaultValue, updateFn, ...rest }) => (
	<Select
		cacheOptions
		options={countryOptions}
		defaultValue={countryOptions.filter((country => country.value === defaultValue))}
		onChange={option => updateFn(option.value)}
		{...rest}
	/>
);

export default CountrySelect;
