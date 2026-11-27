import React from 'react';
import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import Pagination from './Pagination';

vi.mock('@inertiajs/react', async () => {
	const React = await vi.importActual('react');

	return {
		Link: ({ children, ...props }) => React.createElement('a', { ...props, 'data-inertia-link': true }, children),
	};
});

it('uses an Inertia link for numbered pagination pages', () => {
	render(
		<Pagination
			details={{
				total: 60,
				from: 1,
				to: 20,
				prev_page_url: null,
				next_page_url: '/events?filter%5Bdate%5D=all&page=2',
				links: [
					{ label: '1', url: '/events?page=1', active: true },
					{ label: '2', url: '/events?filter%5Bdate%5D=all&page=2', active: false },
				],
			}}
		/>
	);

	expect(screen.getByRole('link', { name: '2' })).toHaveAttribute('data-inertia-link', 'true');
	expect(screen.getByRole('link', { name: '2' })).toHaveAttribute('href', '/events?filter%5Bdate%5D=all&page=2');
});