import React from 'react';
import Icon from '../../../components/Icon';
import TenantSelect from '../../../components/inputs/TenantSelect';
import { createMailTypeFilterDescriptor } from '../../../components/Filters/MailTypeFilterDescriptor';

const renderTenantSelect = ({ value, onChange, options, surface }) => {
	const tenantOptions = options.map(option => ({ label: option.name, value: option.id }));
	const selectedTenant = tenantOptions.find(option => String(option.value) === String(value)) ?? null;

	return (
		<TenantSelect
			options={tenantOptions}
			value={selectedTenant}
			updateFn={onChange}
			className={surface === 'add-filter' ? 'mt-1 mb-3 w-full min-w-0 max-w-full px-3' : undefined}
		/>
	);
};

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
			toolbarMode: 'pill',
			toolbarOnlyWhenNonDefault: true,
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'from',
			label: 'From',
			type: 'text',
			toolbarMode: 'pill',
			toolbarOnlyWhenNonDefault: true,
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
			renderField: renderTenantSelect,
			renderToolbarField: renderTenantSelect,
		},
		createMailTypeFilterDescriptor({ options: mailTypes, defaultValue: defaultMailTypes }),
	];
}
