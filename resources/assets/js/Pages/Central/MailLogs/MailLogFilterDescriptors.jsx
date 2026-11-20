import React from 'react';
import Icon from '../../../components/Icon';
import TenantSelect from '../../../components/inputs/TenantSelect';

export function createCentralMailLogFilterDescriptors({ tenants, mailTypes, defaultMailTypes }) {
	return [
		{
			name: 'subject',
			label: 'Subject',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'to',
			label: 'To',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'from',
			label: 'From',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'tenant',
			label: 'Tenant',
			type: 'choice',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
			options: tenants.map(({ id, name }) => ({ id, name })),
			renderOption: option => (
				<span className="inline-flex items-center gap-1.5">
					<Icon icon="building" />
					{option.name}
				</span>
			),
			renderField: ({ value, onChange, options }) => (
				<TenantSelect
					options={options.map(option => ({ label: option.name, value: option.id }))}
					defaultValue={options
						.map(option => ({ label: option.name, value: option.id }))
						.find(option => String(option.value) === String(value)) ?? null}
					updateFn={onChange}
				/>
			),
		},
		{
			name: 'type',
			label: 'Mail Type',
			type: 'choice',
			multiple: true,
			defaultValue: defaultMailTypes,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: mailTypes,
		},
	];
}
