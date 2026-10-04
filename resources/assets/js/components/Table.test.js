import React from 'react';
import { render, screen } from '@testing-library/react';
import Table, { THead } from './Table';

describe('THead', () => {
	it('sticks to the top of the scrolling table', () => {
		const { container } = render(
			<table>
				<THead>
					<tr>
						<th>Title</th>
					</tr>
				</THead>
			</table>
		);

		expect(container.querySelector('thead').className).toContain('sticky top-0');
		expect(container.querySelector('thead').className).toContain('shadow-[0_1px_0_0_#e5e7eb]');
	});

	it('does not place an overflow boundary between the header and table scroller', () => {
		const { container } = render(
			<Table>
				<THead>
					<tr>
						<th>Title</th>
					</tr>
				</THead>
			</Table>
		);

		expect(container.querySelector('table').parentElement.parentElement.className).not.toContain('overflow-hidden');
	});

	it('places pagination outside the scrolling area', () => {
		const { container } = render(
			<Table pagination={<div>Pagination</div>}>
				<THead>
					<tr>
						<th>Title</th>
					</tr>
				</THead>
			</Table>
		);

		const pagination = screen.getByText('Pagination');

		expect(pagination.parentElement.className).toContain('flex-col');
		expect(pagination.parentElement.firstElementChild.className).toContain('overflow-auto');
	});
});