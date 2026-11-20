import React from 'react';
import Icon from '../../../components/Icon';
import {
	createSingerChoiceFilter,
	createSingerEnsembleFilters,
	createSingerStatusFilter,
	createSingerVoicePartFilter,
} from '../../../components/SingerFilterDescriptors';

export function createAttendanceFilterDescriptors({ voiceParts, ensembles, singerStatuses }) {
	return [
		{
			name: 'user.name',
			label: 'Name',
			type: 'text',
			defaultValue: '',
			showActions: true,
			showDefaultMarker: true,
		},
		createSingerVoicePartFilter(voiceParts, { drawerOrder: 2, clearLabel: 'None' }),
		...createSingerEnsembleFilters(ensembles, {
			name: 'enrolments.ensemble_id',
			drawerOrder: 4,
			clearLabel: 'None',
		}),
		createSingerChoiceFilter({
			name: 'attendance.response',
			label: 'Attendance Response',
			defaultValue: [],
			drawerOrder: 3,
			options: [
				{ id: 'present', name: 'On Time', icon: 'check', colour: 'text-emerald-500' },
				{ id: 'late', name: 'Late', icon: 'alarm-snooze', colour: 'text-yellow-500' },
				{ id: 'unknown', name: 'Not recorded', icon: 'circle', colour: 'text-gray-500' },
				{
					id: 'late_deemed_absent',
					name: 'Late (Deemed Absent)',
					icon: 'alarm-exclamation',
					colour: 'text-orange-500',
				},
				{ id: 'absent', name: 'Absent', icon: 'times', colour: 'text-red-500' },
			],
			renderOption: (option, compact) => (
				<span
					className={`inline-flex items-center gap-1.5 font-medium text-gray-500 ${compact ? 'text-xs' : ''}`}
				>
					<Icon icon={option.icon} className={option.colour} />
					{option.name}
				</span>
			),
		}),
		createSingerStatusFilter({
			statuses: singerStatuses,
			name: 'status.id',
			label: 'Singer Status',
			defaultValue: [],
			drawerOrder: 1,
			clearLabel: 'None',
		}),
	];
}
