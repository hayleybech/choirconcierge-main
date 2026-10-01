import React from 'react';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { router, usePage } from '@inertiajs/react';
import NotificationsMenu from './NotificationsMenu';

jest.mock('@inertiajs/react', () => ({
	usePage: jest.fn(),
	router: {
		patch: jest.fn(),
		post: jest.fn(),
		reload: jest.fn(),
		visit: jest.fn(),
	},
}));

beforeAll(() => {
	global.route = jest.fn(name => name);
});

describe('NotificationsMenu', () => {
	beforeEach(() => {
		usePage.mockReturnValue({
			props: {
				notifications: {
					items: [
						{
							id: 1,
							subject: 'Test notification',
							body: 'Body',
							url: null,
							read_at: null,
							created_at: new Date().toISOString(),
						},
					],
					unread_count: 1,
					filter: 'all',
				},
			},
		});
	});

	it('performs a partial reload that preserves component state when marking a notification as read', async () => {
		render(<NotificationsMenu />);

		await userEvent.click(screen.getByRole('button', { name: /Notifications/ }));
		await userEvent.click(screen.getByRole('button', { name: 'Mark as read' }));

		expect(router.patch).toHaveBeenCalledWith(
			'notifications.update',
			{},
			expect.objectContaining({
				only: ['notifications'],
				preserveState: true,
				preserveScroll: true,
			})
		);
	});

	it('performs a partial reload that preserves component state when marking all as read', async () => {
		render(<NotificationsMenu />);

		await userEvent.click(screen.getByRole('button', { name: /Notifications/ }));
		await userEvent.click(screen.getByRole('button', { name: /Mark all as read/ }));

		expect(router.post).toHaveBeenCalledWith(
			'notifications.read-all',
			{},
			expect.objectContaining({
				only: ['notifications'],
				preserveState: true,
				preserveScroll: true,
			})
		);
	});

	it('performs a partial reload that preserves component state when switching filters', async () => {
		render(<NotificationsMenu />);

		await userEvent.click(screen.getByRole('button', { name: /Notifications/ }));
		await userEvent.click(screen.getByRole('button', { name: /unread/i }));

		expect(router.reload).toHaveBeenCalledWith(
			expect.objectContaining({
				data: { notification_filter: 'unread' },
				only: ['notifications'],
				preserveState: true,
				preserveScroll: true,
			})
		);
	});
});
