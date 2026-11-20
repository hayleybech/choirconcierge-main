import React from 'react';
import RsvpTag from './Event/RsvpTag';
import {
	createSingerEnsembleFilters,
	createSingerStatusFilter,
	createSingerVoicePartFilter,
} from './SingerFilterDescriptors';

const rsvpResponseOptions = [
	{ id: 'yes', name: 'Going', icon: 'check', colour: 'emerald' },
	{ id: 'maybe', name: 'Maybe', icon: 'question', colour: 'amber' },
	{ id: 'no', name: 'Not going', icon: 'times', colour: 'red' },
	{ id: 'unknown', name: 'No RSVP', icon: 'circle', colour: 'gray' },
];

export function createRsvpFilterDescriptors({ voiceParts, ensembles, singerStatuses }) {
	const memberStatusId = singerStatuses.find(status => status.name === 'Members')?.id;

	return [
		{ name: 'user.name', label: 'Name', type: 'text', defaultValue: '' },
		createSingerStatusFilter({
			statuses: singerStatuses,
			name: 'status.id',
			label: 'Singer Status',
			defaultValue: memberStatusId ? [memberStatusId] : [],
		}),
		createSingerVoicePartFilter(voiceParts),
		...createSingerEnsembleFilters(ensembles),
		{
			name: 'rsvp.response',
			label: 'RSVP Response',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			options: rsvpResponseOptions,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			renderOption: option => <RsvpTag label={option.name} icon={option.icon} colour={option.colour} />,
		},
	];
}
