import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import MailLogFilters from './MailLogFilters';

vi.mock('../../../components/Filters', async () => {
	const React = await vi.importActual('react');
	return {
		default: ({ render }) =>
			React.createElement(
				'div',
				null,
				render({ subject: '', from: '', type: [], read: '', to: [] }, vi.fn()),
			),
	};
});

vi.mock('../../../components/inputs/MailingListSelect', () => ({ default: () => null }));
vi.mock('../../../components/inputs/CheckboxGroup', () => ({ default: () => null }));
vi.mock('../../../components/inputs/FilterActions', () => ({ default: () => null }));
vi.mock('../../../components/inputs/RadioGroup', async () => {
	const React = await vi.importActual('react');
	return {
		default: ({ label, options }) =>
			React.createElement(
				'div',
				null,
				label,
				...options.map(option => React.createElement('span', { key: option.id }, option.name)),
			),
	};
});

const props = { form: {}, mailingLists: [], mailTypes: [] };

describe('mail log read-status filter', () => {
	it('hides read-status filters when inbox is disabled', () => {
		render(<MailLogFilters {...props} inboxEnabled={false} />);

		expect(screen.queryByText('Read status')).toBeNull();
		expect(screen.queryByText('Unread')).toBeNull();
	});

	it('shows read-status filters when inbox is enabled', () => {
		render(<MailLogFilters {...props} inboxEnabled />);

		expect(screen.getByText('Read status')).toBeTruthy();
		expect(screen.getByText('Unread')).toBeTruthy();
	});
});