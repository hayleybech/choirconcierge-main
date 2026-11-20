import React, { useState } from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import MailLogFilters from './MailLogFilters';
import { createMailingListMailLogFilterDescriptors } from './MailLogFilterDescriptors';
import { getDefaultFilterData } from '../../../components/filterDescriptorUtils';

vi.mock('react-responsive', () => ({
	useMediaQuery: vi.fn(() => false),
}));

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: () => '/filters' }),
}));

vi.mock('../../../components/inputs/MailingListSelect', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({ options, updateFn }) =>
			React.createElement(
				'button',
				{ type: 'button', onClick: () => updateFn(options.slice(0, 1).map(option => option.value)) },
				'Select first mailing list'
			),
	};
});

const mailingLists = [{ title: 'Announcements', email: 'announcements@example.com', type_icon: 'users' }];
const mailTypes = [{ id: 'broadcast', name: 'Broadcast' }];

const MailLogFilterForm = ({ inboxEnabled }) => {
	const filters = createMailingListMailLogFilterDescriptors({
		mailingLists,
		mailTypes,
		defaultMailTypes: ['broadcast'],
		inboxEnabled,
	});
	const [data, setData] = useState({ subject: '', to: [], from: '', type: ['broadcast'], read: '' });
	const form = {
		data,
		defaultFilterData: getDefaultFilterData(filters),
		searchableFilterNames: [],
		setData: (name, value) => setData(previousData => ({ ...previousData, [name]: value })),
		submit: vi.fn(),
	};

	return <MailLogFilters form={form} filters={filters} />;
};

describe('mail log read-status filter', () => {
	it('hides read-status filters when inbox is disabled', () => {
		render(<MailLogFilterForm inboxEnabled={false} />);

		expect(screen.queryByText('Read Status')).not.toBeInTheDocument();
		expect(screen.queryByLabelText('Unread')).not.toBeInTheDocument();
	});

	it('shows read-status filters when inbox is enabled', () => {
		render(<MailLogFilterForm inboxEnabled />);

		expect(screen.getByText('Read Status')).toBeInTheDocument();
		expect(screen.getByLabelText('Unread')).toBeInTheDocument();
		expect(screen.getByRole('button', { name: 'Select first mailing list' })).toBeInTheDocument();

		fireEvent.click(screen.getByRole('button', { name: 'Select first mailing list' }));

		expect(screen.getAllByText('1 selected')).toHaveLength(2);
	});
});