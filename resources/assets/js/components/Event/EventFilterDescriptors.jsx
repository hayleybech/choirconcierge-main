import React from 'react';
import EnsembleBadge from '../EnsembleBadge';
import EventTypeBadge from '../EventTypeBadge';
import Icon from '../Icon';

export function createEventFilterDescriptors({ eventTypes, ensembles, userEnsemblesCount }) {
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
			name: 'type.id',
			label: 'Type',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: eventTypes.map(type => ({ id: type.id, name: type.title })),
			renderOption: (option, compact) => <EventTypeBadge eventTypeName={option.name} compact={compact} />,
		},
		{
			name: 'date',
			label: 'Date',
			type: 'choice',
			defaultValue: 'upcoming',
			clearValue: 'all',
			allowEmpty: false,
			showActions: true,
			showDefaultMarker: true,
			options: [
				{ id: 'all', name: 'All', icon: 'exchange' },
				{ id: 'upcoming', name: 'Upcoming', icon: 'arrow-from-left' },
				{ id: 'past', name: 'Past', icon: 'arrow-from-right' },
			],
			renderOption: option => (
				<span className="inline-flex items-center gap-1.5">
					<Icon icon={option.icon} />
					{option.name}
				</span>
			),
			onChange: (value, form) => {
				form.setData('date', value);

				if (value === 'upcoming') {
					form.setData('sort', 'start_date');
					form.setData('sortDir', 'asc');
				}

				if (value === 'past') {
					form.setData('sort', 'start_date');
					form.setData('sortDir', 'desc');
				}
			},
		},
		{
			name: 'ensembles.id',
			label: 'Ensemble',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			visible: userEnsemblesCount > 1,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
			renderOption: (option, compact) => <EnsembleBadge ensemble={option} compact={compact} />,
		},
	];
}
