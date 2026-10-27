import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { act } from 'react-dom/test-utils';
import { usePage } from '@inertiajs/react';
import RsvpRecord from './RsvpRecord';

describe('RsvpRecord', () => {
	beforeEach(() => {
		usePage.mockReturnValue({ props: { tenant: { id: 1 } } });
	});

	it('displays an unknown response without offering it as an option', async () => {
		render(
			<RsvpRecord
				rsvp={{ response: 'unknown', label: 'No RSVP' }}
				singerId={1}
				event={1}
			/>
		);

		await act(async () => {
			fireEvent.click(screen.getByRole('button', { name: /No RSVP/ }));
			await new Promise(resolve => setTimeout(resolve, 50));
		});

		expect(screen.getByRole('menuitem', { name: 'Going' })).toBeTruthy();
		expect(screen.getByRole('menuitem', { name: 'Maybe' })).toBeTruthy();
		expect(screen.getByRole('menuitem', { name: 'Not Going' })).toBeTruthy();
		expect(screen.queryByRole('menuitem', { name: 'No RSVP' })).toBeNull();
	});
});