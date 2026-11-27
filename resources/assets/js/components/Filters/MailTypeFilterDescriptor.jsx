import React from 'react';
import Icon from '../Icon';

const mailTypeIcons = {
	chat: 'comments',
	public: 'envelope-open-text',
	distribution: 'paper-plane',
	broadcast: 'paper-plane',
	inbound: 'envelope-open-text',
	notification: 'bell',
	product: 'bullhorn',
};

export function createMailTypeFilterDescriptor({
	name = 'type',
	label = 'Mail Type',
	options,
	defaultValue = [],
}) {
	return {
		name,
		label,
		type: 'choice',
		multiple: true,
		defaultValue,
		showActions: true,
		showDefaultMarker: true,
		drawerAllValuesLabel: 'All',
		options: options.map(option => ({ ...option, icon: mailTypeIcons[option.id] ?? 'envelope' })),
		renderOption: (option, compact) => (
			<span className="inline-flex items-center gap-1.5">
				<Icon icon={option.icon} size={compact ? 'text-xs' : 'text-sm'} />
				{option.name}
			</span>
		),
	};
}
