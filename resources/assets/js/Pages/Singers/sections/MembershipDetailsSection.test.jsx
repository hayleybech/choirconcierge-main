import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import { MembershipDetailsSection } from './MembershipDetailsSection';

vi.mock('../../../components/SimplePanel', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children }) => React.createElement('div', null, children) };
});

vi.mock('../components/DetailList', async () => {
	const React = await vi.importActual('react');
	return {
		DetailList: ({ children }) => React.createElement('dl', null, children),
		DetailListItem: ({ label, children }) =>
				React.createElement('div', null, React.createElement('dt', null, label), React.createElement('dd', null, children)),
	};
});

vi.mock('../../../components/Badge', () => ({ default: () => null }));
vi.mock('../../../components/FeeStatus', () => ({ default: () => null }));
vi.mock('../../../components/DateTag', () => ({ default: () => null }));
vi.mock('../../../components/SingerStatusTag', () => ({ default: () => null }));
vi.mock('../../../components/Icon', () => ({ default: () => null }));
vi.mock('../../../SingerStatus', () => ({ default: vi.fn(function () {}) }));

vi.mock('../../../components/inputs/ButtonV2', async () => {
	const React = await vi.importActual('react');
	return { default: ({ children, onClick }) => React.createElement('button', { onClick, type: 'button' }, children) };
});

vi.mock('../components/RenewFeesDialog', async () => {
	const React = await vi.importActual('react');
	return { default: ({ isOpen }) => (isOpen ? React.createElement('div', { 'data-testid': 'renew-fees-dialog' }) : null) };
});

const singer = {
	status: { status: 'member' },
	joined_at: null,
	roles: [],
	fee_status: 'paid',
	paid_until: null,
	referrer: null,
	reason_for_joining: null,
	membership_details: null,
	user: { last_login: null },
	created_at: null,
	statuses: [],
};

test('allows authorized users to open the membership fee renewal dialog', () => {
	render(<MembershipDetailsSection singer={singer} can={{ manage_finances: true }} />);

	fireEvent.click(screen.getByRole('button', { name: 'Renew' }));

	expect(screen.getByTestId('renew-fees-dialog')).toBeTruthy();
});

test('does not show the renewal button without finance permission', () => {
	render(<MembershipDetailsSection singer={singer} can={{ manage_finances: false }} />);

	expect(screen.queryByRole('button', { name: 'Renew' })).toBeNull();
});