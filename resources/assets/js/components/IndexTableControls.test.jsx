import React, { useState } from 'react';
import { fireEvent, render, screen, waitFor, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import IndexTableControls from './IndexTableControls';
import TableHeadingSort from './TableHeadingSort';
import { createRiserStackFilterDescriptors } from './RiserStack/RiserStackFilterDescriptors';
import { createSingerAttendanceFilterDescriptors } from '../Pages/Singers/Attendance/SingerAttendanceFilterDescriptors';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn() }));

vi.mock('react-responsive', () => ({
	useMediaQuery: mockUseMediaQuery,
}));

import { useMediaQuery } from 'react-responsive';

vi.mock('popper.js', () => ({
	default: vi.fn(() => ({ destroy: vi.fn() })),
}));

vi.mock('./inputs/DateRangeCalendarInput', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({
			ariaLabel,
			startDate,
			endDate,
			updateFn,
			inline,
			onClose,
			onDraftChange,
			hideActions,
			renderActions,
		}) => {
			const [isOpen, setIsOpen] = React.useState(false);
			const selectedRange = { from: new Date(2026, 2, 1), to: new Date(2026, 2, 31) };
			const selected = {
				from: startDate ? new Date(`${startDate}T00:00:00`) : undefined,
				to: endDate ? new Date(`${endDate}T00:00:00`) : undefined,
			};
			const [draftRange, setDraftRange] = React.useState(selected);
			const updateDraftRange = range => {
				setDraftRange(range);
				onDraftChange?.(range);
			};
			const applyDateRange = () => {
				updateFn(draftRange);
				onClose?.();
			};

			if (inline) {
				return React.createElement(
					'div',
					{
						'data-testid': 'inline-date-range',
						'data-draft-range': `${draftRange.from?.toISOString() ?? ''}|${draftRange.to?.toISOString() ??
							''}`,
					},
					React.createElement(
						'button',
						{ type: 'button', onClick: () => updateDraftRange(selectedRange) },
						'Choose calendar range'
					),
					!hideActions && React.createElement('button', { type: 'button' }, 'Clear selected'),
					!hideActions &&
						React.createElement('button', { type: 'button', onClick: onClose }, 'Cancel date range'),
					!hideActions &&
						React.createElement('button', { type: 'button', onClick: applyDateRange }, 'Apply date range'),
					renderActions?.({
						draftRange,
						updateDraftRange,
						selected,
						close: onClose,
					})
				);
			}

			return React.createElement(
				React.Fragment,
				null,
				React.createElement(
					'button',
					{
						'type': 'button',
						'aria-label': ariaLabel,
						'data-start-date': startDate,
						'data-end-date': endDate,
						'onClick': () => setIsOpen(true),
					},
					'Choose date range'
				),
				isOpen &&
					React.createElement(
						'button',
						{
							type: 'button',
							onClick: () => {
								applyDateRange();
								setIsOpen(false);
							},
						},
						'Apply date range'
					)
			);
		},
	};
});

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
		renderOption: option => <span>{option.name}</span>,
	},
	{
		name: 'team',
		label: 'Team',
		multiple: true,
		defaultValue: [],
		options: [
			{ id: 'choir', name: 'Choir' },
			{ id: 'youth', name: 'Youth' },
		],
		renderOption: option => <span>{option.name}</span>,
	},
	{
		name: 'role',
		label: 'Role',
		multiple: true,
		defaultValue: ['admin'],
		options: [
			{ id: 'admin', name: 'Admin' },
			{ id: 'member', name: 'Member' },
		],
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
	showMobileDateRangeFilter = false,
	includeSortableHeaders = false,
} = {}) => {
	const setData = vi.fn();
	const submit = vi.fn();

	const Controls = () => {
		const [formData, updateFormData] = useState(data);
		const updateData = (name, value) => {
			if (typeof name === 'function') {
				const nextData = name(formData);
				setData(nextData);
				updateFormData(nextData);
				return;
			}

			setData(name, value);
			updateFormData(previousData => ({ ...previousData, [name]: value }));
		};

		const form = { data: formData, setData: updateData, submit };

		return (
			<>
				<IndexTableControls
					filters={controlFilters}
					sorts={controlSorts}
					form={form}
					tableActions={tableActions}
					tableActionContent={tableActionContent}
					bulkEdit={bulkEdit}
					onToggleFilters={onToggleFilters}
					filterLabel={filterLabel}
					showMobileDateRangeFilter={showMobileDateRangeFilter}
				/>
				{includeSortableHeaders && (
					<div>
						<div data-testid="sort-state">{formData.sort}:{formData.sortDir}</div>
						{['full-name', 'status', 'team'].map(sort => (
							<TableHeadingSort key={sort} form={form} sort={sort}>
								{sort}
							</TableHeadingSort>
						))}
					</div>
				)}
			</>
		);
	};

	return { ...render(<Controls />), setData, submit, onToggleFilters };
};

it('submits a desktop filter pill change once when multiple sortable headers share the form', async () => {
	const user = userEvent.setup();
	const { submit } = renderControls({ includeSortableHeaders: true });

	expect(submit).not.toHaveBeenCalled();

	await user.click(screen.getByRole('button', { name: 'Status: Inactive; open filter values' }));
	await user.click(screen.getByRole('button', { name: 'Clear' }));

	await waitFor(() => expect(submit).toHaveBeenCalledTimes(1));
	expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true });
});

it('keeps a desktop filter pill submission when the page rerenders during the debounce', async () => {
	const user = userEvent.setup();
	const submit = vi.fn();
	const RerenderingControls = () => {
		const [formData, updateFormData] = useState(initialData);
		const [, forceRerender] = useState(0);
		const form = {
			data: formData,
			setData: (name, value) => updateFormData(previousData => ({ ...previousData, [name]: value })),
			submit,
		};

		return (
			<>
				<IndexTableControls filters={filters.map(filter => ({ ...filter }))} form={form} />
				<button type="button" onClick={() => forceRerender(previousValue => previousValue + 1)}>
					Rerender page
				</button>
				<div data-testid="status-filter-value">{JSON.stringify(formData.status)}</div>
			</>
		);
	};

	render(<RerenderingControls />);
	await user.click(screen.getByRole('button', { name: 'Status: Inactive; open filter values' }));
	await user.click(screen.getByRole('button', { name: 'Clear' }));

	expect(screen.getByTestId('status-filter-value')).toHaveTextContent('[""]');
	await user.click(screen.getByRole('button', { name: 'Rerender page' }));

	await waitFor(() => expect(submit).toHaveBeenCalledTimes(1));
	expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true });
});

it('updates sort state and submits once when a sortable header is clicked', async () => {
	const user = userEvent.setup();
	const { submit } = renderControls({ includeSortableHeaders: true });

	expect(screen.getByTestId('sort-state')).toHaveTextContent('full-name:asc');
	await user.click(screen.getByRole('button', { name: 'status' }));

	expect(screen.getByTestId('sort-state')).toHaveTextContent('status:desc');
	await waitFor(() => expect(submit).toHaveBeenCalledTimes(1));
	expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true });
});

it('submits mobile sort changes once through the SortDropdown', async () => {
	mockUseMediaQuery.mockReturnValue(false);
	const user = userEvent.setup();
	const { submit, setData } = renderControls();

	await user.click(screen.getByRole('button', { name: 'Sort options' }));
	await user.click(screen.getByRole('radio', { name: 'Status' }));

	expect(setData).toHaveBeenCalledWith('sort', 'status');
	await waitFor(() => expect(submit).toHaveBeenCalledTimes(1));
	expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true });
});

it('uses the secondary clear reset button only when a non-search filter is shown', () => {
	mockUseMediaQuery.mockReturnValue(true);
	const searchOnlyFilter = [{ name: 'search', label: 'Search', type: 'text', defaultValue: '' }];
	const searchOnly = renderControls({ controlFilters: searchOnlyFilter, data: { search: '' } });

	expect(screen.queryByRole('button', { name: 'Reset all filters' })).not.toBeInTheDocument();
	searchOnly.unmount();

	renderControls({
		controlFilters: [
			...searchOnlyFilter,
			{ name: 'status', label: 'Status', defaultValue: '', options: [{ id: 'active', name: 'Active' }] },
		],
		data: { search: '', status: 'active' },
	});

	expect(screen.getByRole('button', { name: 'Reset all filters' })).toBeInTheDocument();
});

it('shows default-aware desktop filters and applies staged values from the secondary panel', async () => {
	const user = userEvent.setup();
	const { container, setData } = renderControls();
	const desktop = container.querySelector('[data-testid="desktop-index-table-controls"]');

	const searchInput = within(desktop).getByRole('searchbox', { name: 'Search members by name' });
	expect(searchInput).toHaveValue('Ada');
	expect(searchInput).toHaveAttribute('placeholder', 'Search');
	expect(within(desktop).getByRole('button', { name: 'Clear members search' })).toBeTruthy();

	const statusPill = within(desktop).getByRole('button', {
		name: 'Status: Inactive; open filter values',
	});
	expect(within(desktop).getByRole('button', { name: 'Role: Admin; open filter values' })).toHaveTextContent('Admin');

	await user.click(within(desktop).getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Team' }));

	const valuePanel = screen.getByRole('group', { name: 'Team values' });
	const choirOption = within(valuePanel).getByLabelText('Choir');
	const applyButton = screen.getByRole('menuitem', { name: 'Apply' });

	expect(setData).not.toHaveBeenCalledWith('team', ['choir']);

	await user.click(choirOption);
	await user.click(applyButton);

	expect(setData).toHaveBeenCalledWith('team', ['choir']);
	expect(within(desktop).getByRole('button', { name: 'Team: Choir; open filter values' })).toBeInTheDocument();

	await user.click(within(desktop).getByRole('button', { name: 'Status: Inactive; open filter values' }));
	await user.click(screen.getByRole('button', { name: 'Clear' }));
	expect(within(desktop).getByRole('button', { name: 'Status: Any; open filter values' })).toBeInTheDocument();
	const statusPanel = screen.getByRole('heading', { name: 'Status' }).closest('.fixed');
	await user.click(within(statusPanel).getByRole('button', { name: 'Default' }));
	expect(within(desktop).getByRole('button', { name: 'Status: Active; open filter values' })).toBeInTheDocument();

	fireEvent.change(within(desktop).getByRole('searchbox', { name: 'Search members by name' }), {
		target: { value: 'Grace' },
	});
	expect(setData).toHaveBeenCalledWith('name', 'Grace');
});

it('hides the add-filter button when no filters are available to add', () => {
	const { container } = renderControls({
		controlFilters: [filters[0]],
		data: { ...initialData, name: '' },
	});
	const desktop = container.querySelector('[data-testid="desktop-index-table-controls"]');

	expect(within(desktop).getByRole('searchbox', { name: 'Search members by name' })).toBeInTheDocument();
	expect(within(desktop).queryByRole('button', { name: 'Add filter' })).not.toBeInTheDocument();
});

it('shows and submits the search input inline on mobile without an empty filter drawer trigger', async () => {
	useMediaQuery.mockReturnValue(false);
	const user = userEvent.setup();
	const { container, setData, submit } = renderControls({
		controlFilters: [filters[0]],
		controlSorts: [],
		data: { ...initialData, name: '' },
	});
	const mobile = container.querySelector('[data-testid="mobile-index-table-controls"]');
	const searchInput = within(mobile).getByRole('searchbox', { name: 'Search members by name' });

	expect(searchInput).toHaveAttribute('placeholder', 'Search');
	expect(within(mobile).queryByRole('button', { name: 'Filter members' })).not.toBeInTheDocument();
	await user.type(searchInput, 'Folder');

	expect(setData).toHaveBeenLastCalledWith('name', 'Folder');
	await waitFor(() => expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true }));
});

it('shows the number of active filters in the mobile filter button', () => {
	useMediaQuery.mockReturnValue(false);
	const { container } = renderControls({
		controlFilters: [
			{
				name: 'type',
				label: 'Type',
				type: 'choice',
				multiple: true,
				defaultValue: [],
				options: [{ id: 'broadcast', name: 'Broadcast' }],
			},
			{
				name: 'read',
				label: 'Read status',
				type: 'choice',
				defaultValue: '',
				options: [{ id: 'read', name: 'Read' }],
			},
			{ name: 'sender', label: 'Sender', type: 'text', toolbarMode: 'pill', defaultValue: '' },
		],
		data: { ...initialData, type: ['broadcast'], read: 'read', sender: 'sender@example.com' },
	});
	const mobile = container.querySelector('[data-testid="mobile-index-table-controls"]');

	expect(within(mobile).getByRole('button', { name: 'Filter members' })).toHaveTextContent('Filters (3)');
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

	expect(within(mobile).queryByRole('button', { name: /open filter values/ })).not.toBeInTheDocument();
	expect(within(mobile).getByRole('checkbox', { name: 'Select all members' })).not.toBeChecked();

	await user.click(within(mobile).getByRole('checkbox', { name: 'Select all members' }));
	expect(toggleAll).toHaveBeenCalledOnce();
	expect(within(mobile).queryByRole('button', { name: 'Select Members' })).not.toBeInTheDocument();
	await user.click(filterButton);
	expect(onToggleFilters).toHaveBeenCalledOnce();

	await user.click(within(mobile).getByRole('button', { name: 'members table actions' }));
	await user.click(screen.getByRole('menuitem', { name: 'Select Members' }));
	expect(bulkAction).toHaveBeenCalledOnce();

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

it('shows mobile bulk selection actions in the overflow menu without other table actions', async () => {
	useMediaQuery.mockReturnValue(false);
	const user = userEvent.setup();
	const bulkAction = vi.fn();
	const { container } = renderControls({
		bulkEdit: {
			action: { label: 'Select Members', icon: 'users', onClick: bulkAction },
		},
		tableActions: [],
	});
	const mobile = container.querySelector('[data-testid="mobile-index-table-controls"]');

	expect(within(mobile).queryByRole('button', { name: 'Select Members' })).not.toBeInTheDocument();
	await user.click(within(mobile).getByRole('button', { name: 'members table actions' }));
	await user.click(screen.getByRole('menuitem', { name: 'Select Members' }));

	expect(bulkAction).toHaveBeenCalledOnce();
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
	const { container, setData } = renderControls({
		controlFilters: [...filters, dateFilter],
		data: { ...initialData, starts_after: '2026-06-07' },
	});
	const desktop = container.querySelector('[data-testid="desktop-index-table-controls"]');
	const dateInput = within(desktop).getByRole('textbox', { name: 'Events After for members' });

	expect(dateInput).toHaveValue('2026-06-07');
	within(desktop)
		.getByRole('button', { name: 'Clear Events After for members' })
		.click();
	expect(setData).toHaveBeenLastCalledWith('starts_after', '');
});

it('adds a date-range filter and updates both configured date bounds', async () => {
	const user = userEvent.setup();
	const dateRangeFilter = {
		name: 'date_range',
		start_name: 'starts_after',
		end_name: 'starts_before',
		label: 'Date Range',
		type: 'daterange',
		defaultValue: '',
	};
	const { setData } = renderControls({
		controlFilters: [...filters, dateRangeFilter],
		data: { ...initialData, starts_after: '', starts_before: '' },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Date Range' }));
	expect(setData).not.toHaveBeenCalled();
	const addFilterValues = screen.getByRole('group', { name: 'Date Range values' });
	expect(within(addFilterValues).queryByLabelText('Date Range date range')).not.toBeInTheDocument();
	const applyButton = within(addFilterValues).getByRole('menuitem', { name: 'Apply' });
	const clearButton = within(addFilterValues).getByRole('menuitem', { name: 'Clear' });
	const cancelButton = within(addFilterValues).getByRole('menuitem', { name: 'Cancel' });
	await user.click(within(addFilterValues).getByText('Choose calendar range'));
	await user.click(clearButton);
	expect(setData).not.toHaveBeenCalled();
	expect(within(addFilterValues).getByTestId('inline-date-range')).toHaveAttribute('data-draft-range', '|');
	await user.click(within(addFilterValues).getByText('Choose calendar range'));
	await user.click(cancelButton);
	expect(setData).not.toHaveBeenCalled();
	expect(screen.queryByRole('group', { name: 'Date Range values' })).not.toBeInTheDocument();
	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Date Range' }));
	const reopenedAddFilterValues = screen.getByRole('group', { name: 'Date Range values' });
	await user.click(within(reopenedAddFilterValues).getByText('Choose calendar range'));
	await user.click(within(reopenedAddFilterValues).getByRole('menuitem', { name: 'Apply' }));

	expect(screen.queryByRole('menuitem', { name: 'Date Range' })).not.toBeInTheDocument();
	expect(setData).toHaveBeenNthCalledWith(1, 'starts_after', '2026-03-01');
	expect(setData).toHaveBeenNthCalledWith(2, 'starts_before', '2026-03-31');
	await user.click(screen.getByRole('button', { name: /Date Range:.*open filter values/ }));
	const filterPanel = screen.getByRole('heading', { name: 'Date Range' }).closest('.fixed');

	expect(within(filterPanel).getByTestId('inline-date-range')).toBeInTheDocument();
	expect(within(filterPanel).queryByLabelText('Date Range date range')).not.toBeInTheDocument();
	expect(within(filterPanel).queryByRole('button', { name: 'Clear selected' })).not.toBeInTheDocument();
	expect(within(filterPanel).queryByRole('button', { name: 'Cancel date range' })).not.toBeInTheDocument();
	expect(within(filterPanel).getAllByRole('button', { name: 'Default' })).toHaveLength(1);
	await user.click(within(filterPanel).getByText('Choose calendar range'));
	expect(setData).toHaveBeenCalledTimes(2);
	await user.click(within(filterPanel).getByRole('button', { name: 'Apply' }));

	expect(screen.queryByRole('heading', { name: 'Date Range' })).not.toBeInTheDocument();
	expect(setData).toHaveBeenCalledTimes(4);
	expect(setData).toHaveBeenNthCalledWith(3, 'starts_after', '2026-03-01');
	expect(setData).toHaveBeenNthCalledWith(4, 'starts_before', '2026-03-31');
});

it('serializes singer attendance date selections from the mobile toolbar', async () => {
	useMediaQuery.mockReturnValue(false);
	const user = userEvent.setup();
	const attendanceFilters = createSingerAttendanceFilterDescriptors([]);
	const { setData } = renderControls({
		controlFilters: attendanceFilters,
		data: {
			'type.id': [],
			'starts_after': '2026-03-01',
			'starts_before': '2026-03-31',
			'sort': 'start-time',
			'sortDir': 'asc',
		},
		showMobileDateRangeFilter: true,
	});

	await user.click(screen.getByRole('button', { name: 'Attendance date range' }));
	await user.click(screen.getByRole('button', { name: 'Apply date range' }));

	expect(setData).toHaveBeenNthCalledWith(1, 'starts_after', '2026-03-01');
	expect(setData).toHaveBeenNthCalledWith(2, 'starts_before', '2026-03-31');
});

it('shows date-range pills before other filters', () => {
	const dateRangeFilter = {
		name: 'date_range',
		start_name: 'starts_after',
		end_name: 'starts_before',
		label: 'Date Range',
		type: 'daterange',
		defaultValue: { from: new Date(2026, 2, 1), to: new Date(2026, 2, 31) },
	};
	renderControls({
		controlFilters: [filters[1], dateRangeFilter],
		data: { status: ['active'], starts_after: '2026-03-01', starts_before: '2026-03-31' },
	});

	const filterButtons = within(screen.getByTestId('desktop-index-table-controls')).getAllByRole('button', {
		name: /open filter values/,
	});

	expect(filterButtons[0]).toHaveAccessibleName(/Date Range:/);
	expect(filterButtons[1]).toHaveAccessibleName(/Status:/);
});

it('places a Select All action at the right of a multi-filter popover header', async () => {
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
	const allButton = within(header).getByRole('button', { name: 'Select All' });

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
	const allButton = within(valuePanel).getByText('Select All', { selector: 'button' });
	expect(allButton).toHaveAccessibleName('Select All');
	await user.click(allButton);

	expect(within(valuePanel).getByLabelText('Paid')).toBeChecked();
	expect(within(valuePanel).getByLabelText('Expired')).toBeChecked();
	expect(setData).not.toHaveBeenCalledWith('fee_status', ['paid', 'expired']);

	const applyButton = screen.getByRole('menuitem', { name: 'Apply' });
	await user.click(applyButton);

	expect(setData).toHaveBeenCalledWith('fee_status', ['paid', 'expired']);
});


it('closes the entire Add Filter menu when closing staged values without applying them', async () => {
	const user = userEvent.setup();
	const feeFilter = {
		name: 'fee_status',
		label: 'Fee Status',
		multiple: true,
		defaultValue: [],
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
	await user.click(screen.getByRole('menuitem', { name: 'Close' }));

	expect(setData).not.toHaveBeenCalledWith('fee_status', ['paid']);
	expect(screen.queryByRole('group', { name: 'Fee Status values' })).not.toBeInTheDocument();
	expect(screen.queryByRole('menuitem', { name: 'Fee Status' })).not.toBeInTheDocument();

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Fee Status' }));
	expect(within(screen.getByRole('group', { name: 'Fee Status values' })).getByLabelText('Paid')).not.toBeChecked();
});

it('omits the Any choice from Add Filter values while still selecting a single value', async () => {
	const user = userEvent.setup();
	const visibilityFilter = {
		name: 'visibility',
		label: 'Visibility',
		defaultValue: '',
		options: [
			{ id: 'public', name: 'Public' },
			{ id: 'private', name: 'Private' },
		],
	};
	const { setData } = renderControls({
		controlFilters: [...filters, visibilityFilter],
		data: { ...initialData, visibility: '' },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Visibility' }));
	const valuePanel = screen.getByRole('group', { name: 'Visibility values' });

	expect(within(valuePanel).queryByLabelText('Any')).not.toBeInTheDocument();
	expect(within(valuePanel).queryByText('Any')).not.toBeInTheDocument();

	await user.click(within(valuePanel).getByLabelText('Private'));
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(setData).toHaveBeenCalledWith('visibility', 'private');
});

it('keeps sort controls open and exposes the selected direction accessibly', async () => {
	const user = userEvent.setup();
	const { submit, setData } = renderControls();

	await user.click(screen.getByRole('button', { name: 'Sort: Name' }));
	await user.click(screen.getByRole('radio', { name: 'Status' }));
	expect(screen.getByText('Sort by')).toBeInTheDocument();

	const descendingButton = screen.getByRole('button', { name: 'Descending' });
	const ascendingButton = screen.getByRole('button', { name: 'Ascending' });
	await user.click(descendingButton);

	expect(screen.getByText('Sort by')).toBeInTheDocument();
	expect(descendingButton).toHaveAttribute('aria-pressed', 'true');
	expect(ascendingButton).toHaveAttribute('aria-pressed', 'false');
	expect(descendingButton.parentElement).toHaveAttribute('role', 'group');
	expect(descendingButton.parentElement).toHaveAttribute('aria-label', 'Sort direction');
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
	const { setData, submit } = renderControls({
		controlFilters: [...filters, dateFilter],
		data: { ...initialData, date_range: '' },
		includeSortableHeaders: true,
	});
	expect(submit).not.toHaveBeenCalled();

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
	await waitFor(() => expect(submit).toHaveBeenCalledTimes(1));
	expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true });
});

it('closes an Add Filter text panel without applying its value', async () => {
	const user = userEvent.setup();
	const { setData } = renderControls({
		controlFilters: [{ name: 'from', label: 'From', type: 'text', toolbarMode: 'pill', defaultValue: '' }],
		data: { from: '' },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'From' }));

	const valuesPanel = screen.getByRole('group', { name: 'From values' });
	const input = within(valuesPanel).getByLabelText('Value for From');
	await user.type(input, 'sender@example.com');
	await user.click(within(valuesPanel).getByRole('menuitem', { name: 'Close' }));

	expect(screen.queryByRole('group', { name: 'From values' })).not.toBeInTheDocument();
	expect(setData).not.toHaveBeenCalled();
});

it('applies and resets Riser Stacks ensemble choices', async () => {
	const user = userEvent.setup();
	const riserStackFilters = createRiserStackFilterDescriptors(
		[
			{ id: 1, name: 'Choir' },
			{ id: 2, name: 'Youth' },
		],
		2
	);
	const { setData } = renderControls({
		controlFilters: riserStackFilters,
		data: { 'search': '', 'ensembles.id': [], 'sort': 'full-name', 'sortDir': 'asc' },
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Ensemble' }));
	const valuesPanel = screen.getByRole('group', { name: 'Ensemble values' });
	await user.click(within(valuesPanel).getByLabelText('Choir'));
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(setData).toHaveBeenCalledWith('ensembles.id', [1]);
	const ensemblePill = screen.getByRole('button', { name: 'Ensemble: Choir; open filter values' });

	await user.click(ensemblePill);
	await user.click(
		within(screen.getByRole('heading', { name: 'Ensemble' }).closest('.fixed')).getByRole('button', {
			name: 'Default',
		})
	);

	expect(setData).toHaveBeenLastCalledWith('ensembles.id', []);
});
