import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterFieldRenderer from '../../components/Filters/FilterFieldRenderer';
import { createMailingListFilterDescriptors } from './MailingListFilterDescriptors';
import { createMailingListMailLogFilterDescriptors } from './MailLogs/MailLogFilterDescriptors';
import { createCentralMailLogFilterDescriptors } from '../Central/MailLogs/MailLogFilterDescriptors';

it('renders mailing list type filter options with their type icons', () => {
	const listTypeFilter = createMailingListFilterDescriptors().find(filter => filter.name === 'list_type');

	render(<FilterFieldRenderer filter={listTypeFilter} value={[]} onChange={vi.fn()} variant="toolbar" />);

	[
		['Chat', 'fa-comments'],
		['Public', 'fa-envelope-open-text'],
		['Mailout', 'fa-paper-plane'],
	].forEach(([label, iconClass]) => {
		const option = screen.getByText(label).closest('label');

		expect(option).toBeInTheDocument();
		expect(option.querySelector(`i.${iconClass}`)).toBeInTheDocument();
	});
});

it.each([
	['tenant mail logs', createMailingListMailLogFilterDescriptors],
	['central mail logs', createCentralMailLogFilterDescriptors],
])('renders %s mail type options with their type icons', (_, createFilters) => {
	const mailTypes = [
		{ id: 'broadcast', name: 'Broadcast' },
		{ id: 'inbound', name: 'Inbound' },
		{ id: 'notification', name: 'Notification' },
		{ id: 'product', name: 'Product Announcement' },
	];
	const filters = createFilters === createMailingListMailLogFilterDescriptors
		? createFilters({ mailingLists: [], mailTypes, defaultMailTypes: [] })
		: createFilters({ tenants: [], mailTypes, defaultMailTypes: [] });
	const mailTypeFilter = filters.find(filter => filter.name === 'type');

	render(<FilterFieldRenderer filter={mailTypeFilter} value={[]} onChange={vi.fn()} variant="toolbar" />);

	[
		['Broadcast', 'fa-paper-plane'],
		['Inbound', 'fa-envelope-open-text'],
		['Notification', 'fa-bell'],
		['Product Announcement', 'fa-bullhorn'],
	].forEach(([label, iconClass]) => {
		const option = screen.getByText(label).closest('label');

		expect(option).toBeInTheDocument();
		expect(option.querySelector(`i.${iconClass}`)).toBeInTheDocument();
	});
});