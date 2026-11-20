import React from 'react';
import Badge from './Badge';
import FeeStatus from './FeeStatus';
import EnsembleBadge from './EnsembleBadge';
import SingerStatus from '../SingerStatus';
import SingerStatusTag from './SingerStatusTag';
import VoicePartTag from './VoicePartTag';

const feeStatusOptions = [
	{ id: 'paid', name: 'Paid' },
	{ id: 'expires-soon', name: 'Expires Soon' },
	{ id: 'expired', name: 'Expired' },
	{ id: 'unknown', name: 'Unknown' },
];

export function createSingerStatusFilter({
	statuses,
	name = 'status',
	label = 'Status',
	defaultValue = [],
	...overrides
}) {
	return createSingerChoiceFilter({
		name,
		label,
		defaultValue,
		drawerOptionCompact: true,
		options: statuses.map(status => ({ id: status.id, name: status.name })),
		renderOption: (option, compact) => (
			<SingerStatusTag status={new SingerStatus(option.id)} withLabel compact={compact} />
		),
		...overrides,
	});
}

export function createSingerVoicePartFilter(voiceParts, overrides = {}) {
	return createSingerChoiceFilter({
		name: 'enrolments.voice_part_id',
		label: 'Voice Part',
		defaultValue: [],
		options: voiceParts.map(part => ({ id: part.id, name: part.title, colour: part.colour })),
		renderOption: (option, compact) => (
			<VoicePartTag title={option.name} colour={option.colour} compact={compact} />
		),
		...overrides,
	});
}

export function createSingerEnsembleFilters(ensembles, overrides = {}) {
	if (ensembles.length <= 1) {
		return [];
	}

	return [
		createSingerChoiceFilter({
			name: 'enrolments.ensemble_id',
			label: 'Ensemble',
			defaultValue: [],
			options: ensembles.map(ensemble => ({ id: ensemble.id, name: ensemble.name })),
			renderOption: (option, compact) => <EnsembleBadge ensemble={option} compact={compact} />,
			...overrides,
		}),
	];
}

export function createSingerFeeStatusFilter(overrides = {}) {
	return createSingerChoiceFilter({
		name: 'fee_status',
		label: 'Fee Status',
		defaultValue: [],
		options: feeStatusOptions,
		allValuesLabel: 'All',
		renderOption: (option, compact) => <FeeStatus status={option.id} compact={compact} />,
		...overrides,
	});
}

export function createSingerIndexFilterDescriptors({ statuses, defaultStatus, voiceParts, roles, ensembles }) {
	return [
		{
			name: 'user.name',
			label: 'Name',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
			clearLabel: 'Clear',
			modifiedLabelClassName: 'text-purple-700',
		},
		createSingerStatusFilter({
			statuses,
			defaultValue: defaultStatus ? [defaultStatus] : [],
			clearLabel: 'None',
		}),
		createSingerVoicePartFilter(voiceParts, { clearLabel: 'None' }),
		createSingerChoiceFilter({
			name: 'roles.id',
			label: 'Role',
			defaultValue: [],
			drawerOrder: 4,
			options: roles.map(role => ({ id: role.id, name: role.name })),
			renderOption: (option, compact) => (
				<Badge colour="bg-gray-100 text-gray-800" compact={compact}>
					{option.name}
				</Badge>
			),
			clearLabel: 'None',
		}),
		createSingerFeeStatusFilter({ clearLabel: 'None', drawerOrder: 5 }),
		...createSingerEnsembleFilters(ensembles, { clearLabel: 'None', drawerOrder: 3 }),
	];
}

export function createSingerChoiceFilter({ name, label, defaultValue = [], options, renderOption, ...overrides }) {
	return {
		name,
		label,
		type: 'choice',
		multiple: true,
		defaultValue: [...defaultValue],
		options,
		showActions: true,
		showDefaultMarker: true,
		modifiedLabelClassName: 'text-purple-700',
		drawerAllValuesLabel: 'All',
		renderOption,
		...overrides,
	};
}
