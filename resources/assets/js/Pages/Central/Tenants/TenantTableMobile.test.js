import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import TenantTableMobile from './TenantTableMobile';

jest.mock('../../../components/Pagination', () => () => null);

test('allows filters to be toggled from the mobile table header', () => {
	const setShowFilters = jest.fn();

	render(
		<TenantTableMobile
			tenants={[]}
			pagination={{ total: 0, links: [] }}
			hasNonDefaultFilters={false}
			setShowFilters={setShowFilters}
		/>
	);

	fireEvent.click(screen.getByRole('button', { name: 'Filter/Sort' }));

	expect(setShowFilters).toHaveBeenCalledWith(expect.any(Function));
	expect(setShowFilters.mock.calls[0][0](false)).toBe(true);
});