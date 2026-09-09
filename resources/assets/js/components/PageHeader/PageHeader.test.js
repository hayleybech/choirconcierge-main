import React from 'react';
import { render, screen } from '@testing-library/react';
import { PageHeader2 } from './PageHeader';

describe('PageHeader2', () => {
	it('renders the supplied page header content', () => {
		render(
			<PageHeader2>
				<h1>Singers</h1>
			</PageHeader2>
		);

		expect(screen.getByRole('heading', { name: 'Singers' })).toBeTruthy();
	});
});
