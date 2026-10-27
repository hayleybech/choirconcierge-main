import React from 'react';
import { render, screen } from '@testing-library/react';
import { TableCell } from './Table';

test('uses compact vertical padding when requested', () => {
	render(
		<table>
			<tbody>
				<tr>
					<TableCell compact>Compact cell</TableCell>
				</tr>
			</tbody>
		</table>
	);

	expect(screen.getByText('Compact cell').closest('td').className).toContain('py-2');
});

test('keeps the default table cell padding when compact is omitted', () => {
	render(
		<table>
			<tbody>
				<tr>
					<TableCell>Default cell</TableCell>
				</tr>
			</tbody>
		</table>
	);

	expect(screen.getByText('Default cell').closest('td').className).toContain('py-4');
});