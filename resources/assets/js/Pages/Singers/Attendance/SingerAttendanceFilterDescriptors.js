export function createSingerAttendanceFilterDescriptors(eventTypes) {
	return [
		{
			name: 'type.id',
			label: 'Event Type',
			type: 'choice',
			multiple: true,
			defaultValue: [],
			options: eventTypes.map(type => ({ id: type.id, name: type.title })),
			showActions: true,
			showDefaultMarker: true,
			drawerAllValuesLabel: 'All',
		},
		{
			name: 'date_range',
			start_name: 'starts_after',
			end_name: 'starts_before',
			label: 'Date Range',
			type: 'daterange',
			ariaLabel: 'Attendance date range',
			fieldClassName: 'relative z-[2] space-y-2',
			defaultValue: { from: '', to: '' },
		},
	];
}