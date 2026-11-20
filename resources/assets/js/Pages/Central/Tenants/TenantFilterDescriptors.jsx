import React from 'react';
import Icon from '../../../components/Icon';

export function createTenantFilterDescriptors() {
	return [
		{
			name: 'id',
			label: 'Tenant ID',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'billing_status',
			label: 'Billing Status',
			type: 'choice',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
			options: [
				{ id: 'active', name: 'Active', icon: 'check-circle', textColour: 'text-green-500' },
				{ id: 'subscribed', name: 'Subscribed', icon: 'usd-circle', textColour: 'text-green-500' },
				{ id: 'gratis', name: 'Gratis', icon: 'heart-circle', textColour: 'text-green-500' },
				{ id: 'trial', name: 'Trial', icon: 'dot-circle', textColour: 'text-blue-500' },
				{ id: 'inactive', name: 'Inactive', icon: 'times-circle', textColour: 'text-red-500' },
			],
			renderOption: (option, compact) => (
				<span className="inline-flex items-center gap-1.5">
					<Icon icon={option.icon} className={compact ? `text-xs ${option.textColour}` : option.textColour} />
					{option.name}
				</span>
			),
		},
	];
}
