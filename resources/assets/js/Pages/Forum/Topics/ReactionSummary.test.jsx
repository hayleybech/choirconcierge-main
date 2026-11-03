import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import ReactionSummary from './ReactionSummary';

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: (name, params) => `/${name}/${params.singer}` }),
}));

describe('ReactionSummary', () => {
	it('shows reactor names and profile pictures on hover', () => {
		const reactors = [
			{ id: 1, name: 'Alex Singer', avatar_url: '/alex.jpg' },
			{ id: 2, name: 'Bailey Singer', avatar_url: '/bailey.jpg' },
		];
		const { container } = render(<ReactionSummary reactions={[{ emoji: '🎉', count: 2, reactors }]} />);
		const reaction = screen.getByRole('img', { name: '🎉 reaction, 2, by Alex Singer, Bailey Singer' });

		fireEvent.mouseEnter(reaction);

		const tooltip = screen.getByRole('tooltip');
		expect(tooltip.querySelector('a[href="/singers.show/1"]')?.textContent).toBe('Alex Singer');
		expect(tooltip.querySelector('a[href="/singers.show/2"]')?.textContent).toBe('Bailey Singer');
		expect([...tooltip.querySelectorAll('img')].map(image => image.getAttribute('src'))).toEqual([
			'/alex.jpg',
			'/bailey.jpg',
		]);
		expect(document.body.contains(tooltip)).toBe(true);
		expect(container.contains(tooltip)).toBe(false);
		expect(tooltip.className).toContain('fixed');
	});
});
