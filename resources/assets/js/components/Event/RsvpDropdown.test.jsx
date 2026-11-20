import React from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import { router } from '@inertiajs/react';
import { vi } from 'vitest';
import RsvpDropdown from './RsvpDropdown';

vi.mock('@inertiajs/react', async () => {
	const React = await vi.importActual('react');

	return {
		router: { visit: vi.fn() },
		Link: React.forwardRef(({ children, ...props }, ref) =>
			React.createElement('a', { ref, ...props }, children)
		),
	};
});

vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: routeName => routeName }) }));

describe('RsvpDropdown', () => {
	beforeEach(() => {
		router.visit.mockClear();
	});

	it('allows a member to add details when responding from an event page', async () => {
		render(
			<RsvpDropdown
				event={{
					id: 1,
					tenant_id: 1,
					my_rsvp: { id: null, response: 'yes', label: 'Going', icon: 'check' },
				}}
			/>
		);

		fireEvent.click(screen.getByRole('button', { name: /Going/ }));
		const dialog = await screen.findByRole('dialog');
		fireEvent.click(within(dialog).getByRole('button', { name: 'Not Going' }));
		fireEvent.change(within(dialog).getByRole('textbox', { name: 'Reason for not going' }), {
			target: { value: 'I will be away' },
		});
		fireEvent.click(within(dialog).getByRole('button', { name: 'Save RSVP' }));

		expect(router.visit).toHaveBeenCalledWith(
			'events.rsvps.store',
			expect.objectContaining({
				method: 'post',
				data: { rsvp_response: 'no', details: 'I will be away' },
				preserveScroll: true,
			})
		);
	});
});