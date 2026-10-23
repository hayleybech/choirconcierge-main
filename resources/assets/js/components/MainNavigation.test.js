import React from 'react';
import { render, screen } from '@testing-library/react';
import { usePage } from '@inertiajs/react';
import MainNavigation from './MainNavigation';

jest.mock('@inertiajs/react', () => ({
	usePage: jest.fn(),
	Link: ({ children, ...props }) => <a {...props}>{children}</a>,
}));
jest.mock('../hooks/useRoute', () => () => ({ route: name => `/${name}` }));
jest.mock('./Icon', () => () => null);

describe('MainNavigation inbox badge', () => {
	beforeEach(() => {
		usePage.mockReturnValue({
			props: {
				user: { name: 'Singer', email: 'singer@example.com', avatar_url: '' },
				unreadCommunicationsCount: 4,
			},
		});
	});

	it('shows the unread communications count next to Inbox', () => {
		render(
			<MainNavigation
				navigation={[{ name: 'Inbox', route: 'communications.index', icon: 'mail', items: [] }]}
			/>,
		);

		expect(screen.getByText('Inbox')).toBeTruthy();
		expect(screen.getByLabelText('4 unread communications')).toBeTruthy();
	});

	it('does not show a badge when there are no unread communications', () => {
		usePage.mockReturnValue({
			props: {
				user: { name: 'Singer', email: 'singer@example.com', avatar_url: '' },
				unreadCommunicationsCount: 0,
			},
		});

		render(
			<MainNavigation
				navigation={[{ name: 'Inbox', route: 'communications.index', icon: 'mail', items: [] }]}
			/>,
		);

		expect(screen.queryByLabelText(/unread communications/)).toBeNull();
	});
});
