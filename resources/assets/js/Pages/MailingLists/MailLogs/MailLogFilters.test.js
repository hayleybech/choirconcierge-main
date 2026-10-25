import React from 'react';
import { render, screen } from '@testing-library/react';
import MailLogFilters from './MailLogFilters';

jest.mock('../../../components/Filters', () => {
	const React = require('react');
	return ({ render }) =>
		React.createElement(
			'div',
			null,
			render({ subject: '', from: '', type: [], read: '', to: [] }, jest.fn()),
		);
});

jest.mock('../../../components/inputs/MailingListSelect', () => () => null);
jest.mock('../../../components/inputs/CheckboxGroup', () => () => null);
jest.mock('../../../components/inputs/FilterActions', () => () => null);
jest.mock('../../../components/inputs/RadioGroup', () => {
	const React = require('react');
	return ({ label, options }) =>
		React.createElement(
			'div',
			null,
			label,
			...options.map(option => React.createElement('span', { key: option.id }, option.name)),
		);
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