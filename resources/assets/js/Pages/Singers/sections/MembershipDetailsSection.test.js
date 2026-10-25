import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { MembershipDetailsSection } from './MembershipDetailsSection';

jest.mock('../../../components/SimplePanel', () => {
	const React = require('react');
	return ({ children }) => React.createElement('div', null, children);
});

jest.mock('../components/DetailList', () => {
	const React = require('react');
	return {
		DetailList: ({ children }) => React.createElement('dl', null, children),
		DetailListItem: ({ label, children }) =>
				React.createElement('div', null, React.createElement('dt', null, label), React.createElement('dd', null, children)),
	};
});

jest.mock('../../../components/Badge', () => () => null);
jest.mock('../../../components/FeeStatus', () => () => null);
jest.mock('../../../components/DateTag', () => () => null);
jest.mock('../../../components/SingerStatusTag', () => () => null);
jest.mock('../../../components/Icon', () => () => null);
jest.mock('../../../SingerStatus', () => jest.fn(function () {}));

jest.mock('../../../components/inputs/ButtonV2', () => {
	const React = require('react');
	return ({ children, onClick }) => React.createElement('button', { onClick, type: 'button' }, children);
});

jest.mock('../components/RenewFeesDialog', () => {
	const React = require('react');
	return ({ isOpen }) => (isOpen ? React.createElement('div', { 'data-testid': 'renew-fees-dialog' }) : null);
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