import React, { useState } from 'react';
import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterDrawer from './FilterDrawer';
import Filters from './Filters';

const mocks = vi.hoisted(() => ({
	setData: vi.fn(),
	submit: vi.fn(),
	closeDrawer: vi.fn(),
}));

vi.mock('react-responsive', () => ({
	useMediaQuery: vi.fn(),
}));

vi.mock('../hooks/useRoute', () => ({
	default: () => ({ route: () => '/filters' }),
}));

import { useMediaQuery } from 'react-responsive';

const FilterForm = ({ searchableFilterNames = [] }) => {
	const [data, updateData] = useState({ name: 'Existing' });

	return (
		<FilterDrawer closeFn={mocks.closeDrawer}>
			<Filters
				routeName="filters.index"
				form={{
					data,
					defaultFilterData: { name: '' },
					searchableFilterNames,
					setData: (nameOrData, value) => {
						mocks.setData(nameOrData, value);
						updateData(previousData =>
							typeof nameOrData === 'object'
								? nameOrData
								: { ...previousData, [nameOrData]: value }
						);
					},
					submit: mocks.submit,
				}}
				render={(filterData, setFilterData) => (
					<label>
						Name
						<input
							aria-label="Name filter"
							value={filterData.name}
							onChange={event => setFilterData('name', event.target.value)}
						/>
					</label>
				)}
			/>
		</FilterDrawer>
	);
};

beforeEach(() => {
	vi.clearAllMocks();
	useMediaQuery.mockReturnValue(false);
});

it('stages mobile filter changes until Apply', async () => {
	render(<FilterForm />);
	const nameFilter = screen.getByRole('textbox', { name: 'Name filter' });

	fireEvent.change(nameFilter, { target: { value: 'Draft name' } });

	expect(mocks.setData).not.toHaveBeenCalled();
	expect(mocks.submit).not.toHaveBeenCalled();

	fireEvent.change(nameFilter, { target: { value: 'Applied name' } });
	fireEvent.click(screen.getByRole('button', { name: 'Apply' }));

	await waitFor(() => expect(mocks.setData).toHaveBeenCalledWith({ name: 'Applied name' }));
	await waitFor(() => expect(mocks.submit).toHaveBeenCalledOnce());
	expect(mocks.closeDrawer).toHaveBeenCalledOnce();
});

it('applies default values and closes the drawer when Clear all is clicked', async () => {
	render(<FilterForm searchableFilterNames={['name']} />);
	const nameFilter = screen.getByRole('textbox', { name: 'Name filter' });

	fireEvent.change(nameFilter, { target: { value: 'Draft name' } });
	fireEvent.click(screen.getByRole('button', { name: 'Clear all' }));

	expect(nameFilter).toHaveValue('');
	await waitFor(() => expect(mocks.setData).toHaveBeenCalledWith({ name: '' }));
	await waitFor(() => expect(mocks.submit).toHaveBeenCalledOnce());
	expect(mocks.closeDrawer).toHaveBeenCalledOnce();
});