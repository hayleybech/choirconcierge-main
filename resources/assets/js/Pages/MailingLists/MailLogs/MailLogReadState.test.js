import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import MailLogTableDesktop from './MailLogTableDesktop';
import MailLogTableMobile from './MailLogTableMobile';

vi.mock('@inertiajs/react', async () => {
	const React = await vi.importActual('react');
	return { Link: ({ children, ...props }) => React.createElement('a', props, children) };
});

vi.mock('../../../hooks/useRoute', () => ({ default: () => ({ route: name => `/${name}` }) }));
vi.mock('../../../components/Icon', () => ({ default: () => null }));
vi.mock('../../../components/DateTag', () => ({ default: () => null }));
vi.mock('../../../components/Pagination', () => ({ default: () => null }));
vi.mock('../../../components/MailStatusTag', () => ({
	__esModule: true,
	default: () => null,
	mailTypeIcons: { broadcast: 'mail' },
}));
vi.mock('../../../components/TableHeadingSort', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('span', null, children) };
});
vi.mock('../../../components/Table', async () => {
	const React = await vi.importActual('react');
	return {
		__esModule: true,
		default: ({ children }) => React.createElement('table', null, children),
		TableCell: ({ children, className }) => React.createElement('td', { className }, children),
		THead: ({ children }) => React.createElement('thead', null, children),
		TBody: ({ children }) => React.createElement('tbody', null, children),
		TableHeading: ({ children }) => React.createElement('th', null, children),
	};
});
vi.mock('../../../components/TableMobile', async () => {
	const React = await vi.importActual('react');
	return {
		__esModule: true,
		default: ({ children }) => React.createElement('ul', null, children),
		TableMobileHeader: ({ children }) => React.createElement('div', null, children),
		TableMobileLink: ({ children, url }) => React.createElement('a', { href: url }, children),
	};
});
vi.mock('../../../components/inputs/ButtonV2', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('button', null, children) };
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
			{ showAdminDetails: true, hasNonDefaultFilters: false, setShowFilters: vi.fn() },
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