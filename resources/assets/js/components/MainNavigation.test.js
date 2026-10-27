import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import { usePage } from '@inertiajs/react';
import MainNavigation from './MainNavigation';

vi.mock('@inertiajs/react', () => ({
    usePage: vi.fn(),
	Link: ({ children, ...props }) => <a {...props}>{children}</a>,
}));
vi.mock('../hooks/useRoute', () => ({ default: () => ({ route: name => `/${name}` }) }));
vi.mock('./Icon', () => ({ default: () => null }));

describe('MainNavigation inbox badge', () => {
	beforeEach(() => {
		usePage.mockReturnValue({
			props: {
				user: { name: 'Singer', email: 'singer@example.com', avatar_url: '' },
				inboxEnabled: true,
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
				inboxEnabled: true,
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

	it('hides the unread badge when inbox is disabled', () => {
		usePage.mockReturnValue({
			props: {
				user: { name: 'Singer', email: 'singer@example.com', avatar_url: '' },
				inboxEnabled: false,
				unreadCommunicationsCount: 4,
			},
		});

		render(
			<MainNavigation
				navigation={[{ name: 'Communications', route: 'communications.index', icon: 'mail', items: [] }]}
			/>,
		);

		expect(screen.queryByLabelText(/unread communications/)).toBeNull();
	});
});
