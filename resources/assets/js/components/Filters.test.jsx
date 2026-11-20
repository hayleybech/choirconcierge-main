import React, { useState } from 'react';
import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterDrawer from './FilterDrawer';
import Filters from './Filters';
import { createRiserStackFilterDescriptors } from './RiserStack/RiserStackFilterDescriptors';

const mocks = vi.hoisted(() => ({
	setData: vi.fn(),
	submit: vi.fn(),
	closeDrawer: vi.fn(),
	submittedData: null,
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
						if (typeof nameOrData === 'object') {
							mocks.setData(nameOrData);
						} else {
							mocks.setData(nameOrData, value);
						}
						updateData(previousData =>
							typeof nameOrData === 'object' ? nameOrData : { ...previousData, [nameOrData]: value }
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

const riserStackFilters = createRiserStackFilterDescriptors(
	[
		{ id: 1, name: 'Choir' },
		{ id: 2, name: 'Youth' },
	],
	2
);

const RiserStackFilterForm = ({ initialData = { 'search': '', 'ensembles.id': [] } }) => {
	const [data, updateData] = useState(initialData);

	return (
		<FilterDrawer closeFn={mocks.closeDrawer}>
			<Filters
				routeName="stacks.index"
				filters={riserStackFilters}
				form={{
					data,
					defaultFilterData: { 'search': '', 'ensembles.id': [] },
					searchableFilterNames: ['search'],
					setData: (nameOrData, value) => {
						if (typeof nameOrData === 'object') {
							mocks.setData(nameOrData);
						} else {
							mocks.setData(nameOrData, value);
						}
						updateData(previousData =>
							typeof nameOrData === 'object' ? nameOrData : { ...previousData, [nameOrData]: value }
						);
					},
					submit: () => {
						mocks.submittedData = data;
						mocks.submit();
					},
				}}
			/>
		</FilterDrawer>
	);
};

beforeEach(() => {
	vi.clearAllMocks();
	mocks.submittedData = null;
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

it('renders styled Riser Stacks fields and stages ensemble selections until Apply', async () => {
	render(<RiserStackFilterForm />);
	const choirOption = screen.getByLabelText('Choir');

	expect(screen.getByText('Ensemble')).toBeInTheDocument();
	expect(screen.getByText('Default')).toBeInTheDocument();
	expect(screen.getByText('Choir').closest('span')).toHaveClass('bg-gray-100');

	fireEvent.click(choirOption);

	expect(mocks.setData).not.toHaveBeenCalled();
	expect(screen.getByText('1 selected')).toBeInTheDocument();

	fireEvent.click(screen.getByRole('button', { name: 'Reset Ensemble filter' }));

	expect(screen.getByText('0 selected')).toBeInTheDocument();
	expect(screen.getByText('Default')).toBeInTheDocument();
	expect(mocks.setData).not.toHaveBeenCalled();
	fireEvent.click(choirOption);

	fireEvent.click(screen.getByRole('button', { name: 'Apply' }));

	await waitFor(() => expect(mocks.setData).toHaveBeenCalledWith({ 'search': '', 'ensembles.id': [1] }));
	await waitFor(() => expect(mocks.submit).toHaveBeenCalledOnce());
	expect(mocks.closeDrawer).toHaveBeenCalledOnce();
});

it('preserves all form data when a desktop drawer filter changes', async () => {
	const initialData = {
		'search': 'Existing search',
		'ensembles.id': [],
		'sort': 'full-name',
		'sortDir': 'desc',
	};
	useMediaQuery.mockReturnValue(true);

	render(<RiserStackFilterForm initialData={initialData} />);
	fireEvent.click(screen.getByLabelText('Choir'));

	await waitFor(() => expect(mocks.submit).toHaveBeenCalledOnce());
	expect(mocks.submittedData).toEqual({
		...initialData,
		'ensembles.id': [1],
	});
});
