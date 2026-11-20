import React from 'react';
import Icon from './Icon';
import LearningStatus from '../LearningStatus';
import {
	createSingerEnsembleFilters,
	createSingerStatusFilter,
	createSingerVoicePartFilter,
} from './SingerFilterDescriptors';

export function createLearningStatusFilterDescriptors({ voiceParts, ensembles, singerStatuses }) {
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
		{
			name: 'learning.status',
			label: 'Learning Status',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			options: Object.keys(LearningStatus.statuses).map(slug => ({
				id: slug,
				name: new LearningStatus(slug).title,
			})),
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			renderOption: option => {
				const status = new LearningStatus(option.id);

				return (
					<span className="inline-flex items-center gap-1.5 text-gray-500">
						<Icon icon={status.icon} className={status.textColour} />
						{status.title}
					</span>
				);
			},
		},
		...createSingerEnsembleFilters(ensembles),
	];
}
