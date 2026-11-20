import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import EventType from '../EventType';
import { createEventFilterDescriptors } from './Event/EventFilterDescriptors';
import EventTypeBadge from './EventTypeBadge';

it('shares event type badge colors and compact sizing with filter options', () => {
	const filters = createEventFilterDescriptors({
		eventTypes: [{ id: 1, title: 'Rehearsal' }],
		ensembles: [],
		userEnsemblesCount: 1,
	});
	const typeFilter = filters.find(filter => filter.name === 'type.id');
	const filterOption = typeFilter.renderOption({ name: 'Rehearsal' }, true);

	expect(filterOption.type).toBe(EventTypeBadge);

	render(
		<>
			{filterOption}
			<EventTypeBadge eventTypeName="Rehearsal" />
		</>
	);

	const badges = screen.getAllByText('Rehearsal').map(option => option.closest('span'));

	expect(badges).toHaveLength(2);
	expect(badges[0]).toHaveClass(new EventType('Rehearsal').badgeColour, 'px-1', 'py-0');
	expect(badges[1]).toHaveClass(new EventType('Rehearsal').badgeColour, 'px-2', 'py-0.5');
});