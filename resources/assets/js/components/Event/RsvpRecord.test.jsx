import React from 'react';
import { fireEvent, render, screen, waitFor, within } from '@testing-library/react';
import { router, usePage } from '@inertiajs/react';
import { vi } from 'vitest';
import RsvpRecord from './RsvpRecord';

vi.mock('@inertiajs/react', async () => {
	const React = await vi.importActual('react');

	return {
		usePage: vi.fn(),
		router: { put: vi.fn() },
		Link: React.forwardRef(({ children, ...props }, ref) =>
			React.createElement('a', { ref, ...props }, children)
		),
	};
});

vi.mock('../../hooks/useRoute', () => ({ default: () => ({ route: routeName => routeName }) }));

describe('RsvpRecord', () => {
	beforeEach(() => {
		usePage.mockReturnValue({ props: { tenant: { id: 1 } } });
		router.put.mockClear();
	});

	it('displays an unknown response and offers the available options in a modal', async () => {
		render(
			<RsvpRecord
				rsvp={{ response: 'unknown', label: 'No RSVP' }}
				singerId={1}
				event={1}
			/>
		);

		fireEvent.click(screen.getByRole('button', { name: /No RSVP/ }));
		const dialog = await screen.findByRole('dialog');

		expect(within(dialog).getByRole('button', { name: 'Save RSVP' })).toBeDisabled();
		expect(within(dialog).getByRole('button', { name: 'Going' })).toBeTruthy();
		expect(within(dialog).getByRole('button', { name: 'Maybe' })).toBeTruthy();
		expect(within(dialog).getByRole('button', { name: 'Not Going' })).toBeTruthy();
		expect(within(dialog).queryByRole('button', { name: 'No RSVP' })).toBeNull();
	});

	it('allows a manager to add details when updating a singer response', async () => {
		render(
			<RsvpRecord
				rsvp={{ response: 'unknown', label: 'No RSVP' }}
				singerId={1}
				event={1}
			/>
		);

		fireEvent.click(screen.getByRole('button', { name: /No RSVP/ }));
		const dialog = await screen.findByRole('dialog');
		fireEvent.click(within(dialog).getByRole('button', { name: 'Maybe' }));
		fireEvent.change(within(dialog).getByRole('textbox', { name: 'RSVP details' }), {
			target: { value: 'I may arrive late' },
		});
		fireEvent.click(within(dialog).getByRole('button', { name: 'Save RSVP' }));

		await waitFor(() =>
			expect(router.put).toHaveBeenCalledWith(
				'events.rsvps.updateSinger',
				{ response: 'maybe', details: 'I may arrive late' },
				expect.objectContaining({ preserveScroll: true })
			)
		);
	});
});