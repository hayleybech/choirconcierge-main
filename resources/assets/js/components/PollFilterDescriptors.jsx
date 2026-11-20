import React from 'react';
import EnsembleBadge from './EnsembleBadge';
import Icon from './Icon';

export function createPollFilterDescriptors({ ensembles, showEnsembleColumn }) {
	return [
		{
			name: 'title',
			label: 'Title',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		{
			name: 'status',
			label: 'Status',
			type: 'choice',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
			options: [
				{ id: 'open', name: 'Open', icon: 'lock-open', textColour: 'text-emerald-700' },
				{ id: 'closed', name: 'Closed', icon: 'lock', textColour: 'text-gray-600' },
			],
			renderOption: option => (
				<span className={`inline-flex items-center gap-1.5 ${option.textColour}`}>
					<Icon icon={option.icon} />
					{option.name}
				</span>
			),
		},
		{
			name: 'ensembles.id',
			label: 'Ensemble',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			visible: showEnsembleColumn,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
			renderOption: (option, compact) => <EnsembleBadge ensemble={option} compact={compact} />,
		},
	];
}
