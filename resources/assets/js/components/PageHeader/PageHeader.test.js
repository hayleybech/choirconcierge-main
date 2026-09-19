import React from 'react';
import { render, screen } from '@testing-library/react';
import { PageHeader } from './PageHeader';

describe('PageHeader2', () => {
	it('renders the supplied page header content', () => {
		render(
			<PageHeader>
				<h1>Singers</h1>
			</PageHeader>
		);

		expect(screen.getByRole('heading', { name: 'Singers' })).toBeTruthy();
	});

	it('uses the mobile page header padding below the layout top-bar spacing', () => {
		const { container } = render(<PageHeader><h1>Singers</h1></PageHeader>);

		expect(container.firstChild.classList.contains('pt-4')).toBe(true);
	});
});
