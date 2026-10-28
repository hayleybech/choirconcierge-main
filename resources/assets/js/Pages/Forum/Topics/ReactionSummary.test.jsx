import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import ReactionSummary from './ReactionSummary';

describe('ReactionSummary', () => {
	it('shows reactor names and profile pictures on hover', () => {
		const reactors = [
			{ id: 1, name: 'Alex Singer', avatar_url: '/alex.jpg' },
			{ id: 2, name: 'Bailey Singer', avatar_url: '/bailey.jpg' },
		];
		const { container } = render(<ReactionSummary reactions={[{ emoji: '🎉', count: 2, reactors }]} />);
		const reaction = screen.getByRole('img', { name: '🎉 reaction, 2, by Alex Singer, Bailey Singer' });

		fireEvent.mouseEnter(reaction);

		const tooltip = container.querySelector('[role="tooltip"]');
		expect(tooltip.textContent).toContain('Alex Singer');
		expect(tooltip.textContent).toContain('Bailey Singer');
		expect([...tooltip.querySelectorAll('img')].map(image => image.getAttribute('src'))).toEqual([
			'/alex.jpg',
			'/bailey.jpg',
		]);
		expect(tooltip.parentElement.className).toContain('group-hover:visible');
	});
});