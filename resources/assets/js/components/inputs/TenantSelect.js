import React from 'react';
import { components } from 'react-select';
import Select from 'react-select';
import Icon from '../Icon';
import reactSelectStyles from './reactSelectStyles';

const TenantSelect = ({ options, defaultValue, updateFn, isClearable = true }) => (
	<div className="mt-1">
		<Select
			cacheOptions
			options={options}
			components={{ SingleValue, Option }}
			isClearable={isClearable}
			defaultValue={defaultValue}
			onChange={option => updateFn(option?.value ?? '')}
			styles={reactSelectStyles}
		/>
	</div>
);

export default TenantSelect;

const SingleValue = ({ data: { label }, children, ...props }) => (
	<components.SingleValue {...props}>
		<span className="text-gray-700 text-sm">
			<Icon icon="building" mr />
			{label}
		</span>
	</components.SingleValue>
);

const Option = ({ data: { label }, children, isSelected, ...props }) => (
	<components.Option {...props} isSelected={isSelected}>
		<div className={`${isSelected ? 'text-white' : 'text-gray-700'} text-sm`}>
			<Icon icon="building" mr />
			{label}
		</div>
	</components.Option>
);
