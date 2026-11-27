import { vi } from 'vitest';
import { getClearedFilterValue } from '../Filters/utils';
import { createEventFilterDescriptors } from './EventFilterDescriptors';

it('clears the event date filter to All and updates sort state for past events', () => {
	const filters = createEventFilterDescriptors({
		eventTypes: [{ id: 1, title: 'Rehearsal' }],
		ensembles: [{ id: 1, name: 'Choir' }],
		userEnsemblesCount: 1,
	});
	const dateFilter = filters.find(filter => filter.name === 'date');
	const setData = vi.fn();

	expect(dateFilter.defaultValue).toBe('upcoming');
	expect(getClearedFilterValue(dateFilter)).toBe('all');
	expect(filters.find(filter => filter.name === 'ensembles.id').visible).toBe(false);

	dateFilter.onChange('past', { setData });

	expect(setData.mock.calls).toEqual([
		['date', 'past'],
		['sort', 'start_date'],
		['sortDir', 'desc'],
	]);
});