import React, { useState } from 'react';
import { render, screen, waitFor, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterDrawer from '../../../components/FilterDrawer';
import SingerAttendanceFilters from './SingerAttendanceFilters';
import { createSingerAttendanceFilterDescriptors } from './SingerAttendanceFilterDescriptors';

const mocks = vi.hoisted(() => ({ setData: vi.fn(), submit: vi.fn(), closeDrawer: vi.fn(), submittedData: null }));

vi.mock('react-responsive', () => ({
	useMediaQuery: vi.fn(),
}));

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: () => '/singers/1/attendance' }),
}));

vi.mock('@inertiajs/react', () => ({
	usePage: () => ({ props: { user: {} } }),
}));

vi.mock('@headlessui/react', async () => {
	const React = await vi.importActual('react');
	const Wrapper = ({ as: Component = 'div', children, ...props }) =>
		React.createElement(
			Component,
			props,
			typeof children === 'function' ? children({ close: vi.fn(), open: true }) : children
		);

	return {
		Popover: Object.assign(Wrapper, { Button: Wrapper, Panel: Wrapper }),
		Transition: ({ children }) => React.createElement('div', { 'data-testid': 'transition-wrapper' }, children),
	};
});

vi.mock('react-day-picker', async () => {
	const React = await vi.importActual('react');

	return {
		DayPicker: ({ mode, onSelect }) =>
			React.createElement(
				'div',
				{ 'data-testid': 'day-picker', 'data-mode': mode },
				React.createElement(
					'button',
					{
						type: 'button',
						onClick: () => onSelect({ from: new Date(2025, 2, 1), to: new Date(2025, 2, 31) }),
					},
					'Choose range'
				)
			),
	};
});

vi.mock('react-day-picker/style.css', () => ({}));

import { useMediaQuery } from 'react-responsive';

const eventTypes = [
	{ id: 'rehearsal', title: 'Rehearsal' },
	{ id: 'concert', title: 'Concert' },
];
const filters = createSingerAttendanceFilterDescriptors(eventTypes);

const AttendanceFilterForm = ({ initialData } = {}) => {
	const [data, updateData] = useState(
		initialData ?? {
			'type.id': [],
			'starts_after': '',
			'starts_before': '',
			'sort': 'start-time',
		}
	);

	return (
		<FilterDrawer closeFn={mocks.closeDrawer}>
			<SingerAttendanceFilters
				filters={filters}
				singer={{ id: 1 }}
				form={{
					data,
					defaultFilterData: { 'type.id': [], 'starts_after': '', 'starts_before': '' },
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

it('shows attendance type counts and restores the default selection before applying', async () => {
	render(<AttendanceFilterForm />);
	const user = userEvent.setup();
	const eventTypeField = screen.getByText('Event Type').closest('fieldset');

	expect(within(eventTypeField).getByText('0 selected')).toBeInTheDocument();
	expect(within(eventTypeField).queryByTitle('Filter differs from default')).not.toBeInTheDocument();
	await user.click(screen.getByLabelText('Rehearsal'));
	expect(within(eventTypeField).getByText('1 selected')).toBeInTheDocument();
	expect(within(eventTypeField).getByTitle('Filter differs from default')).toBeInTheDocument();

	await user.click(within(eventTypeField).getByRole('button', { name: 'Reset Event Type filter' }));
	expect(screen.getByLabelText('Rehearsal')).not.toBeChecked();
	expect(within(eventTypeField).queryByTitle('Filter differs from default')).not.toBeInTheDocument();
	await user.click(within(eventTypeField).getByRole('button', { name: 'Select All' }));
	await user.click(screen.getAllByRole('button', { name: 'Apply' })[1]);

	await waitFor(() =>
		expect(mocks.setData).toHaveBeenCalledWith(expect.objectContaining({ 'type.id': ['rehearsal', 'concert'] }))
	);
	await waitFor(() => expect(mocks.submit).toHaveBeenCalledOnce());
	expect(mocks.submittedData['type.id']).toEqual(['rehearsal', 'concert']);
	expect(mocks.closeDrawer).toHaveBeenCalledOnce();
});

it('serializes selected attendance date bounds to the route query keys on Apply', async () => {
	render(<AttendanceFilterForm />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Choose range' }));
	await user.click(screen.getAllByRole('button', { name: 'Apply' })[0]);
	await user.click(screen.getAllByRole('button', { name: 'Apply' })[1]);

	await waitFor(() =>
		expect(mocks.setData).toHaveBeenCalledWith(
			expect.objectContaining({ starts_after: '2025-03-01', starts_before: '2025-03-31' })
		)
	);
	await waitFor(() => expect(mocks.submit).toHaveBeenCalledOnce());
	expect(mocks.submittedData).toMatchObject({ starts_after: '2025-03-01', starts_before: '2025-03-31' });
});

it('clears both attendance date bounds when the range is reset and applied', async () => {
	render(
		<AttendanceFilterForm
			initialData={{
				'type.id': [],
				'starts_after': '2025-03-01',
				'starts_before': '2025-03-31',
				'sort': 'start-time',
			}}
		/>
	);
	const user = userEvent.setup();

	await user.click(screen.getByRole('textbox', { name: 'Attendance date range' }));
	await user.click(screen.getByRole('button', { name: 'Reset' }));
	await user.click(screen.getAllByRole('button', { name: 'Apply' })[0]);
	await user.click(screen.getAllByRole('button', { name: 'Apply' })[1]);

	await waitFor(() =>
		expect(mocks.setData).toHaveBeenCalledWith(expect.objectContaining({ starts_after: '', starts_before: '' }))
	);
	expect(mocks.submittedData).toMatchObject({ starts_after: '', starts_before: '' });
});
