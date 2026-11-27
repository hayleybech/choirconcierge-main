import React from 'react';
import {
	createSingerChoiceFilter,
	createSingerVoicePartFilter,
} from '../../components/SingerFilterDescriptors';

export function createAttendanceReportFilterDescriptors({
	eventTypes,
	voiceParts,
	defaultEventType,
	defaultStartsAfter,
	defaultStartsBefore,
	dateRangePresets,
}) {
	return [
		createSingerChoiceFilter({
			name: 'type.id',
			label: 'Event Type',
			defaultValue: [defaultEventType],
			drawerOrder: 1,
			options: eventTypes.map(type => ({ id: type.id, name: type.title })),
		}),
		{
			name: 'date_range',
			start_name: 'starts_after',
			end_name: 'starts_before',
			label: 'Date Range',
			type: 'daterange',
			defaultValue: { from: defaultStartsAfter, to: defaultStartsBefore },
			startMonth: new Date(2017, 0),
			endMonth: new Date(new Date().getFullYear() + 5, 11),
			ariaLabel: 'Attendance report date range',
			drawerOrder: 0,
			children: dateRangePresets,
		},
		createSingerVoicePartFilter(voiceParts, { drawerOrder: 2, clearLabel: 'None' }),
	];
}
