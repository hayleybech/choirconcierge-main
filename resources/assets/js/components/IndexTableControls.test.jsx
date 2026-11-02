import React, { useState } from 'react';
import { fireEvent, render, screen, waitFor, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import IndexTableControls from './IndexTableControls';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn() }));

vi.mock('react-responsive', () => ({
	useMediaQuery: mockUseMediaQuery,
}));

import { useMediaQuery } from 'react-responsive';

vi.mock('popper.js', () => ({
	default: vi.fn(() => ({ destroy: vi.fn() })),
}));

beforeEach(() => {
	useMediaQuery.mockReturnValue(true);
});

const filters = [
	{ name: 'name', label: 'Name', type: 'text', defaultValue: '' },
	{
		name: 'status',
		label: 'Status',
		multiple: true,
		defaultValue: ['active'],
		options: [
			{ id: 'active', name: 'Active' },
			{ id: 'inactive', name: 'Inactive' },
		],
		renderOption: (option, compact) => (
			<span data-testid={`status-${option.id}`} data-compact={compact}>
				{option.name}
			</span>
		),
	},
	{
		name: 'team',
		label: 'Team',
		multiple: true,
		defaultValue: [],
		options: [{ id: 'choir', name: 'Choir' }, { id: 'youth', name: 'Youth' }],
		renderOption: (option, compact) => (
			<span data-testid={`team-${option.id}`} data-compact={compact}>
				{option.name}
			</span>
		),
	},
	{
		name: 'role',
		label: 'Role',
		multiple: true,
		defaultValue: ['admin'],
		options: [{ id: 'admin', name: 'Admin' }, { id: 'member', name: 'Member' }],
	},
];

const sorts = [
	{ id: 'full-name', name: 'Name' },
	{ id: 'status', name: 'Status' },
];

const initialData = {
	name: 'Ada',
	status: ['inactive'],
	team: [],
	role: ['admin'],
	fee_status: [],
	sort: 'full-name',
	sortDir: 'asc',
};

const renderControls = ({
	data = initialData,
	controlFilters = filters,
	controlSorts = sorts,
	tableActions = [],
	tableActionContent = null,
	bulkEdit,
	onToggleFilters = vi.fn(),
	filterLabel = 'members',
} = {}) => {
	const setData = vi.fn();
	const submit = vi.fn();

	const Controls = () => {
		const [formData, updateFormData] = useState(data);
		const updateData = (name, value) => {
			setData(name, value);
			updateFormData(previousData => ({ ...previousData, [name]: value }));
		};

		return (
			<IndexTableControls
				filters={controlFilters}
				sorts={controlSorts}
				form={{ data: formData, setData: updateData, submit }}
				tableActions={tableActions}
				tableActionContent={tableActionContent}
				bulkEdit={bulkEdit}
				onToggleFilters={onToggleFilters}
				filterLabel={filterLabel}
			/>
		);
	};

	return { ...render(<Controls />), setData, submit, onToggleFilters };
};

it('shows default-aware desktop filters and applies staged values from the secondary panel', async () => {
	const user = userEvent.setup();
	const { container, setData } = renderControls();
	const desktop = container.querySelector('[data-testid="desktop-index-table-controls"]');

	expect(desktop).toHaveClass('hidden', 'lg:flex');
	const searchInput = within(desktop).getByRole('searchbox', { name: 'Search members by name' });
	expect(searchInput).toHaveValue('Ada');
	expect(searchInput.parentElement.querySelector('.fa-search')).toBeTruthy();
	expect(within(desktop).getByRole('button', { name: 'Clear members search' })).toBeTruthy();

	const statusPill = within(desktop).getByRole('button', {
		name: 'Status: Inactive; open filter values',
	});
	expect(statusPill).toHaveClass('bg-purple-50', 'border-purple-200');
	expect(within(statusPill).getByTestId('status-inactive')).toHaveAttribute('data-compact', 'true');
	expect(within(desktop).getByRole('button', { name: 'Role: Admin; open filter values' }).querySelector('.bg-gray-100')).toHaveTextContent('Admin');

	await user.click(within(desktop).getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Team' }));

	const valuePanel = screen.getByRole('group', { name: 'Team values' });
	const choirOption = within(valuePanel).getByLabelText('Choir');
	const applyButton = screen.getByRole('menuitem', { name: 'Apply' });

	expect(within(valuePanel).getByTestId('team-choir')).toHaveAttribute('data-compact', 'false');
	expect(choirOption.closest('.overflow-y-auto')).not.toContainElement(applyButton);
	expect(setData).not.toHaveBeenCalledWith('team', ['choir']);

	await user.click(choirOption);
	await user.click(applyButton);

	expect(setData).toHaveBeenCalledWith('team', ['choir']);
	expect(within(desktop).getByRole('button', { name: 'Team: Choir; open filter values' })).toBeInTheDocument();

	await user.click(within(desktop).getByRole('button', { name: 'Status: Inactive; open filter values' }));
	await user.click(screen.getByRole('button', { name: 'Clear selected' }));
	expect(within(desktop).getByRole('button', { name: 'Status: Any; open filter values' })).toBeInTheDocument();
	const statusPanel = screen.getByRole('heading', { name: 'Status' }).closest('.fixed');
	await user.click(within(statusPanel).getByRole('button', { name: 'Reset to default' }));
	expect(within(desktop).getByRole('button', { name: 'Status: Active; open filter values' })).toBeInTheDocument();

	fireEvent.change(within(desktop).getByRole('searchbox', { name: 'Search members by name' }), {
		target: { value: 'Grace' },
	});
	expect(setData).toHaveBeenCalledWith('name', 'Grace');
});

it('provides mobile bulk selection, filter, sort, and action menu controls', async () => {
	useMediaQuery.mockReturnValue(false);
	const user = userEvent.setup();
	const toggleAll = vi.fn();
	const bulkAction = vi.fn();
	const invite = vi.fn();
	const onToggleFilters = vi.fn();
	const bulkEdit = {
		isActiveMobile: true,
		selectedIds: ['member-1'],
		totalItems: 2,
		refSelectAll: React.createRef(),
		toggleAll,
		action: { label: 'Select Members', icon: 'users', onClick: bulkAction },
	};
	const { container, submit } = renderControls({
		bulkEdit,
		onToggleFilters,
		tableActions: [
			{ label: 'Invite', icon: 'user-plus', onClick: invite },
			{ label: 'Export', icon: 'file-export', url: '/members/export', download: true },
		],
	});
	const mobile = container.querySelector('[data-testid="mobile-index-table-controls"]');
	const filterButton = within(mobile).getByRole('button', { name: 'Filter members' });

	expect(mobile).toHaveClass('flex', 'lg:hidden');
	expect(within(mobile).queryByRole('button', { name: /open filter values/ })).not.toBeInTheDocument();
	expect(filterButton).toHaveClass('bg-purple-50', 'border-purple-200');
	expect(within(mobile).getByRole('checkbox', { name: 'Select all members' })).not.toBeChecked();

	await user.click(within(mobile).getByRole('checkbox', { name: 'Select all members' }));
	expect(toggleAll).toHaveBeenCalledOnce();
	await user.click(within(mobile).getByRole('button', { name: 'Select Members' }));
	expect(bulkAction).toHaveBeenCalledOnce();
	await user.click(filterButton);
	expect(onToggleFilters).toHaveBeenCalledOnce();

	await user.click(within(mobile).getByRole('button', { name: 'members table actions' }));
	await user.click(screen.getByRole('menuitem', { name: 'Invite' }));
	expect(invite).toHaveBeenCalledOnce();

	await user.click(within(mobile).getByRole('button', { name: 'members table actions' }));
	expect(screen.getByRole('menuitem', { name: 'Export' })).toHaveAttribute('download');
	await user.click(within(mobile).getByRole('button', { name: 'Sort options' }));
	expect(screen.getByRole('radio', { name: 'Name' })).toBeInTheDocument();
	await user.click(screen.getByRole('radio', { name: 'Status' }));
	await waitFor(() => expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true }));
});

it('does not show the overflow menu when there are no extra table actions', () => {
	const { container } = renderControls({ tableActions: [] });

	expect(container.querySelector('[data-testid="mobile-index-table-controls"]')).toBeTruthy();
	expect(screen.queryByRole('button', { name: 'members table actions' })).not.toBeInTheDocument();
});

it('renders custom table actions inside the mobile overflow menu', async () => {
	useMediaQuery.mockReturnValue(false);
	const user = userEvent.setup();
	const { container } = renderControls({ tableActionContent: <button type="button">Columns</button> });
	const mobile = container.querySelector('[data-testid="mobile-index-table-controls"]');

	await user.click(within(mobile).getByRole('button', { name: 'members table actions' }));
	expect(within(mobile).getByRole('button', { name: 'Columns' })).toBeInTheDocument();
});

it('renders date filters as compact date inputs and clears their values', () => {
	const dateFilter = {
		name: 'starts_after',
		label: 'Events After',
		type: 'date',
		defaultValue: '',
	};
	const { setData } = renderControls({
		controlFilters: [...filters, dateFilter],
		data: { ...initialData, starts_after: '2026-06-07' },
	});
	const dateInput = screen.getByRole('textbox', { name: 'Events After for members' });

	expect(dateInput).toHaveValue('2026-06-07');
	screen.getByRole('button', { name: 'Clear Events After for members' }).click();
	expect(setData).toHaveBeenLastCalledWith('starts_after', '');
});

it('places a configured All action at the right of a multi-filter popover header', async () => {
	const user = userEvent.setup();
	const feeFilter = {
		name: 'fee_status',
		label: 'Fee Status',
		multiple: true,
		defaultValue: [],
		allValuesLabel: 'All',
		options: [
			{ id: 'paid', name: 'Paid' },
			{ id: 'expired', name: 'Expired' },
		],
	};
	const { setData } = renderControls({
		controlFilters: [...filters, feeFilter],
		data: { ...initialData, fee_status: [] },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Fee Status' }));
	const valuePanel = screen.getByRole('group', { name: 'Fee Status values' });
	await user.click(within(valuePanel).getByLabelText('Paid'));
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));
	await user.click(screen.getByRole('button', { name: 'Fee Status: Paid; open filter values' }));

	const header = screen.getByRole('heading', { name: 'Fee Status' }).parentElement.parentElement;
	const allButton = within(header).getByRole('button', { name: 'All' });

	expect(header.lastElementChild).toBe(allButton);
	await user.click(allButton);
	expect(setData).toHaveBeenLastCalledWith('fee_status', ['paid', 'expired']);
});

it('stages all configured values from Add Filter before applying them', async () => {
	const user = userEvent.setup();
	const feeFilter = {
		name: 'fee_status',
		label: 'Fee Status',
		multiple: true,
		defaultValue: [],
		allValuesLabel: 'All',
		options: [
			{ id: 'paid', name: 'Paid' },
			{ id: 'expired', name: 'Expired' },
		],
	};
	const { setData } = renderControls({
		controlFilters: [...filters, feeFilter],
		data: { ...initialData, fee_status: [] },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Fee Status' }));
	const valuePanel = screen.getByRole('group', { name: 'Fee Status values' });
	const allButton = within(valuePanel).getByText('All', { selector: 'button' });
	expect(allButton).toHaveAccessibleName('All');
	await user.click(allButton);

	expect(within(valuePanel).getByLabelText('Paid')).toBeChecked();
	expect(within(valuePanel).getByLabelText('Expired')).toBeChecked();
	expect(setData).not.toHaveBeenCalledWith('fee_status', ['paid', 'expired']);

	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(setData).toHaveBeenCalledWith('fee_status', ['paid', 'expired']);
});

it('keeps sort controls open and marks the selected direction with selected intent', async () => {
	const user = userEvent.setup();
	const { submit, setData } = renderControls();

	await user.click(screen.getByRole('button', { name: 'Sort: Name' }));
	await user.click(screen.getByRole('radio', { name: 'Status' }));
	expect(screen.getByText('Sort by')).toBeInTheDocument();

	const descendingButton = screen.getByRole('button', { name: 'Descending' });
	await user.click(descendingButton);

	expect(screen.getByText('Sort by')).toBeInTheDocument();
	expect(descendingButton).toHaveAttribute('aria-pressed', 'true');
	expect(descendingButton).toHaveClass('bg-purple-50', 'border-purple-200');
	expect(screen.getByRole('button', { name: 'Ascending' })).toHaveAttribute('aria-pressed', 'false');
	expect(setData).toHaveBeenCalledWith('sort', 'status');
	expect(setData).toHaveBeenCalledWith('sortDir', 'desc');
	await waitFor(() => expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true }));
});

it('uses a filter change handler to update related form state', async () => {
	const user = userEvent.setup();
	const onChange = vi.fn((value, form) => {
		form.setData('date_range', value);
		form.setData('sort', 'date');
		form.setData('sortDir', value === 'upcoming' ? 'asc' : 'desc');
	});
	const dateFilter = {
		name: 'date_range',
		label: 'Date range',
		options: [
			{ id: 'upcoming', name: 'Upcoming' },
			{ id: 'past', name: 'Past' },
		],
		onChange,
	};
	const { setData } = renderControls({
		controlFilters: [...filters, dateFilter],
		data: { ...initialData, date_range: '' },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Date range' }));
	const valuePanel = screen.getByRole('group', { name: 'Date range values' });
	await user.click(within(valuePanel).getByLabelText('Upcoming'));
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(onChange).toHaveBeenCalledWith(
		'upcoming',
		expect.objectContaining({
			setData: expect.any(Function),
		})
	);
	expect(setData.mock.calls).toEqual([
		['date_range', 'upcoming'],
		['sort', 'date'],
		['sortDir', 'asc'],
	]);
});