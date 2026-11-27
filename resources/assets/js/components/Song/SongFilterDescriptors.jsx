import React from 'react';
import Badge from '../Badge';
import EnsembleBadge from '../EnsembleBadge';
import SongStatus from '../../SongStatus';
import SongStatusTag from '../SongStatusTag';

export function createSongFilterDescriptors({
	statuses,
	defaultStatuses,
	categories,
	userEnsemblesCount,
	ensembles,
	showForProspectsDefault,
}) {
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
			name: 'status.id',
			label: 'Status',
			type: 'choice',
			multiple: true,
			defaultValue: defaultStatuses,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			options: statuses.map(status => ({ id: status.id, name: status.title, slug: status.slug })),
			renderOption: (option, compact) => (
				<SongStatusTag status={new SongStatus(option.slug)} withLabel compact={compact} />
			),
		},
		{
			name: 'categories.id',
			label: 'Category',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			drawerOrder: 4,
			options: categories.map(category => ({ id: category.id, name: category.title })),
			renderOption: (option, compact) => <Badge compact={compact}>{option.name}</Badge>,
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
			drawerOrder: 2,
			options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
			renderOption: (option, compact) => <EnsembleBadge ensemble={option} compact={compact} />,
		},
		{
			name: 'show_for_prospects',
			label: 'Audition Songs',
			type: 'choice',
			multiple: true,
			multipleBool: true,
			defaultValue: [],
			visible: showForProspectsDefault.length > 1,
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
			drawerOrder: 3,
			options: [
				{ id: false, name: 'Non-Audition Songs' },
				{ id: true, name: 'Audition Songs' },
			],
			renderOption: (option, compact) => (
				<Badge
					colour={option.id ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'}
					compact={compact}
				>
					{option.name}
				</Badge>
			),
		},
	];
}
