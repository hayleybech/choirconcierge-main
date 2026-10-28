import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import PollOptionVoters from './PollOptionVoters';

vi.mock('./Dialog', () => ({
	default: ({ children, isOpen, title }) =>
		isOpen ? (
			<div role="dialog" aria-label={title}>
				{children}
			</div>
		) : null,
}));

describe('PollOptionVoters', () => {
	it('opens the full voter list from the collapsed count', () => {
		const voters = [
			{ id: 1, name: 'Alex Singer', avatar_url: '/alex.jpg' },
			{ id: 2, name: 'Bailey Singer', avatar_url: '/bailey.jpg' },
			{ id: 3, name: 'Casey Singer', avatar_url: '/casey.jpg' },
			{ id: 4, name: 'Dana Singer', avatar_url: '/dana.jpg' },
			{ id: 5, name: 'Ellis Singer', avatar_url: '/ellis.jpg' },
		];

		render(<PollOptionVoters option={{ label: 'Option A', voters }} />);

		expect(screen.getByRole('button', { name: 'Show 2 more voters for Option A' }).textContent).toBe('+2');
		fireEvent.click(screen.getByRole('button', { name: 'Show 2 more voters for Option A' }));

		expect(screen.getByRole('dialog', { name: 'Voters for Option A' })).not.toBeNull();
		voters.forEach(voter => expect(screen.getByText(voter.name)).not.toBeNull());
	});
});