import { getClearedFilterValue, getDefaultFilterData, getFilterData } from '../../components/filterDescriptorUtils';
import { createAttendanceReportFilterDescriptors } from './AttendanceReportFilterDescriptors';

it('keeps attendance report date defaults and serializes selected dates to query keys', () => {
	const filters = createAttendanceReportFilterDescriptors({
		eventTypes: [{ id: 4, title: 'Rehearsal' }],
		voiceParts: [{ id: 2, title: 'Soprano', colour: 'purple' }],
		defaultEventType: 4,
		defaultStartsAfter: '2026-03-01',
		defaultStartsBefore: '2026-03-31',
		dateRangePresets: () => null,
	});
	const dateFilter = filters.find(filter => filter.type === 'daterange');

	expect(getDefaultFilterData(filters)).toEqual({
		'type.id': [4],
		starts_after: '2026-03-01',
		starts_before: '2026-03-31',
		'enrolments.voice_part_id': [],
	});
	expect(
		getFilterData(dateFilter, {
			from: new Date(2026, 3, 10),
			to: new Date(2026, 3, 12),
		})
	).toEqual({
		starts_after: '2026-04-10',
		starts_before: '2026-04-12',
	});
	expect(getFilterData(dateFilter, getClearedFilterValue(dateFilter))).toEqual({
		starts_after: '',
		starts_before: '',
	});
});