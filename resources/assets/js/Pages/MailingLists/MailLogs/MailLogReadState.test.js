import React from 'react';
import { render, screen } from '@testing-library/react';
import MailLogTableDesktop from './MailLogTableDesktop';
import MailLogTableMobile from './MailLogTableMobile';

jest.mock('@inertiajs/react', () => {
	const React = require('react');
	return { Link: ({ children, ...props }) => React.createElement('a', props, children) };
});

jest.mock('../../../hooks/useRoute', () => () => ({ route: name => `/${name}` }));
jest.mock('../../../components/Icon', () => () => null);
jest.mock('../../../components/DateTag', () => () => null);
jest.mock('../../../components/Pagination', () => () => null);
jest.mock('../../../components/MailStatusTag', () => ({
	__esModule: true,
	default: () => null,
	mailTypeIcons: { broadcast: 'mail' },
}));
jest.mock('../../../components/TableHeadingSort', () => {
	const React = require('react');
	return ({ children }) => React.createElement('span', null, children);
});
jest.mock('../../../components/Table', () => {
	const React = require('react');
	return {
		__esModule: true,
		default: ({ children }) => React.createElement('table', null, children),
		TableCell: ({ children, className }) => React.createElement('td', { className }, children),
		THead: ({ children }) => React.createElement('thead', null, children),
		TBody: ({ children }) => React.createElement('tbody', null, children),
		TableHeading: ({ children }) => React.createElement('th', null, children),
	};
});
jest.mock('../../../components/TableMobile', () => {
	const React = require('react');
	return {
		__esModule: true,
		default: ({ children }) => React.createElement('ul', null, children),
		TableMobileHeader: ({ children }) => React.createElement('div', null, children),
		TableMobileLink: ({ children, url }) => React.createElement('a', { href: url }, children),
	};
});
jest.mock('../../../components/inputs/ButtonV2', () => {
	const React = require('react');
	return ({ children }) => React.createElement('button', null, children);
});

const log = {
	id: 'mail-log-1',
	uid: 'broadcast-1',
	is_read: false,
	subject: 'Feature update',
	has_attachments: false,
	from: 'Choir admin',
	from_user: null,
	to: 'All members',
	latest_event: { user_group: null },
	size: 0,
	opens_count: 3,
	created_at: '2026-10-24T00:00:00Z',
};

const logs = { data: [log] };

describe('mail log read-state presentation', () => {
	it.each([
		['desktop', MailLogTableDesktop, { showAdminDetails: true, sortFilterForm: {} }, 'tr'],
		[
			'mobile',
			MailLogTableMobile,
			{ showAdminDetails: true, hasNonDefaultFilters: false, setShowFilters: jest.fn() },
			'li',
		],
	])('hides read styling on the %s table when inbox is disabled', (name, Table, props, rowTag) => {
		render(<Table logs={logs} inboxEnabled={false} {...props} />);

		const row = screen.getByText('Feature update').closest(rowTag);
		expect(row.classList.contains('font-bold')).toBe(false);
		expect(row.classList.contains('bg-gray-50')).toBe(false);
		expect(screen.getByText('3')).toBeTruthy();
	});

	it('shows read styling on the desktop table when inbox is enabled', () => {
		render(<MailLogTableDesktop logs={logs} inboxEnabled showAdminDetails={false} sortFilterForm={{}} />);

		expect(screen.getByText('Feature update').closest('tr').classList.contains('font-bold')).toBe(true);
	});
});