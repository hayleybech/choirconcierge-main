import React from 'react';
import { components } from 'react-select';
import Select from 'react-select';
import Icon from '../Icon';
import reactSelectStyles from './reactSelectStyles';

const MailingListSelect = ({ options, defaultValue, updateFn, multiple = false, isClearable = true }) => (
	<div className="mt-1">
		<Select
			cacheOptions
			options={options}
			components={{ SingleValue, MultiValueLabel, Option }}
			isMulti={multiple}
			isClearable={isClearable}
			defaultValue={defaultValue}
			onChange={option =>
				multiple ? updateFn(option?.map(item => item.value) ?? []) : updateFn(option?.value ?? '')
			}
			styles={reactSelectStyles}
		/>
	</div>
);

export default MailingListSelect;

const SingleValue = ({ data: { label, type_icon }, children, ...props }) => (
	<components.SingleValue {...props}>
		<span className="text-gray-700 text-sm">
			<Icon icon={type_icon} mr />
			{label}
		</span>
	</components.SingleValue>
);

const MultiValueLabel = ({ data: { label, type_icon }, children, ...props }) => (
	<components.MultiValueLabel {...props}>
		<span className="text-gray-700 text-sm">
			<Icon icon={type_icon} mr />
			{label}
		</span>
	</components.MultiValueLabel>
);

const Option = ({ data: { label, email, type_icon }, children, isSelected, ...props }) => (
	<components.Option {...props} isSelected={isSelected}>
		<div className={`${isSelected ? 'text-white' : 'text-gray-700'} text-sm`}>
			<Icon icon={type_icon} mr />
			{label}
		</div>
		<div className={`mt-1 ${isSelected ? 'text-purple-100' : 'text-gray-500'} text-xs`}>{email}</div>
	</components.Option>
);
