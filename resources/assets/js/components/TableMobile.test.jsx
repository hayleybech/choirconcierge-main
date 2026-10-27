import React from 'react';
import { render, screen } from '@testing-library/react';
import TableMobile, { TableMobileHeader } from './TableMobile';

describe('TableMobile', () => {
	it('allows the list to grow with the page instead of creating a nested scroll area', () => {
		const { container } = render(
			<TableMobile>
				<li>Table row</li>
			</TableMobile>
		);

		expect(screen.getByText('Table row')).toBeTruthy();
		expect(container.firstChild.className).toBe('');
		expect(container.querySelector('ul').parentElement.className).toBe('');
	});

	it('keeps the mobile table header sticky at the top', () => {
		const { container } = render(
			<TableMobileHeader bulkEdit={{ isActiveMobile: false, noun: 'Singer' }}>Actions</TableMobileHeader>
		);

		expect(container.firstChild.className).toContain('sticky top-[47px]');
	});

	it('applies responsive visibility directly to the sticky header', () => {
		const { container } = render(
			<TableMobileHeader bulkEdit={{ isActiveMobile: false, noun: 'Singer' }} className="lg:hidden">
				Actions
			</TableMobileHeader>
		);

		expect(container.firstChild.className).toContain('lg:hidden');
	});
});