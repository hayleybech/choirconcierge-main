import React, { useState } from 'react';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import IndexTableControls from '../../components/IndexTableControls';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn() }));

vi.mock('react-responsive', () => ({
	useMediaQuery: mockUseMediaQuery,
}));

import { useMediaQuery } from 'react-responsive';

const sorts = [
	{ id: 'full-name', name: 'Name' },
	{ id: 'status-title', name: 'Status' },
];

vi.mock('popper.js', () => ({
	default: vi.fn(() => ({ destroy: vi.fn() })),
}));

const makeBulkEdit = () => ({
	isActiveMobile: true,
	selectedIds: ['singer-1'],
	totalItems: 2,
	refSelectAll: React.createRef(),
	toggleAll: vi.fn(),
	action: {
		label: 'Select Singers',
		icon: 'check-square',
		onClick: vi.fn(),
	},
});

beforeEach(() => {
	useMediaQuery.mockReturnValue(false);
});

it('places selection actions in the overflow menu and opens it from the gear button', async () => {
	const bulkEdit = makeBulkEdit();
	const importSingers = vi.fn();
	const form = {
		data: { sort: 'full-name', sortDir: 'asc' },
		setData: vi.fn(),
		submit: vi.fn(),
	};
	render(
		<IndexTableControls
			filters={[]}
			bulkEdit={bulkEdit}
			onToggleFilters={vi.fn()}
			hasChangedFilters
			sorts={sorts}
			form={form}
			tableActions={[
				{ label: 'Import', icon: 'file-import', onClick: importSingers },
				{ label: 'Export', icon: 'file-export', url: '/singers/export', download: true },
			]}
			filterLabel="singers"
		/>
	);
	const user = userEvent.setup();

	const selectAll = screen.getByRole('checkbox', { name: 'Select all singers' });
	const filterButton = screen.getByRole('button', { name: 'Filter singers' });
	const sortButton = screen.getByRole('button', { name: 'Sort options' });
	const actionsButton = screen.getByRole('button', { name: 'singers table actions' });

	expect(selectAll).toHaveProperty('checked', false);
	expect(screen.queryByRole('button', { name: 'Select Singers' })).not.toBeInTheDocument();
	expect(filterButton.compareDocumentPosition(sortButton) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
	expect(sortButton.compareDocumentPosition(actionsButton) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
	expect(filterButton).toHaveClass('h-8', 'py-1', 'px-1.5');
	expect(sortButton).toHaveClass('h-8', 'py-1', 'px-1.5');
	expect(filterButton).toHaveClass('bg-purple-50', 'border-purple-200');
	expect(actionsButton).toHaveClass('h-8');

	await user.click(actionsButton);
	expect(screen.getByRole('menuitem', { name: 'Import' })).toBeTruthy();
	expect(screen.getByRole('menuitem', { name: 'Export' }).getAttribute('download')).toBe('');
	await user.click(screen.getByRole('menuitem', { name: 'Select Singers' }));
	expect(bulkEdit.action.onClick).toHaveBeenCalledOnce();

	await user.click(actionsButton);
	await user.click(screen.getByRole('menuitem', { name: 'Import' }));
	expect(importSingers).toHaveBeenCalledOnce();
});

it('shows the mobile Cancel Selection action in the overflow menu', async () => {
	const bulkEdit = {
		...makeBulkEdit(),
		action: {
			...makeBulkEdit().action,
			label: 'Cancel Selection',
		},
	};

	const user = userEvent.setup();
	render(
		<IndexTableControls
			filters={[]}
			bulkEdit={bulkEdit}
			onToggleFilters={vi.fn()}
			sorts={sorts}
			form={{ data: { sort: 'full-name', sortDir: 'asc' }, setData: vi.fn() }}
			tableActions={[]}
			filterLabel="singers"
		/>
	);

	await user.click(screen.getByRole('button', { name: 'singers table actions' }));
	expect(screen.getByRole('menuitem', { name: 'Cancel Selection' })).toBeInTheDocument();
});

it('selects all singers from the toolbar checkbox', async () => {
	const bulkEdit = makeBulkEdit();
	render(
		<IndexTableControls
			filters={[]}
			bulkEdit={bulkEdit}
			onToggleFilters={vi.fn()}
			sorts={sorts}
			form={{ data: { sort: 'full-name', sortDir: 'asc' }, setData: vi.fn() }}
			tableActions={[]}
			filterLabel="singers"
		/>
	);

	await userEvent.setup().click(screen.getByRole('checkbox', { name: 'Select all singers' }));

	expect(bulkEdit.toggleAll).toHaveBeenCalledOnce();
});

it('submits mobile sort changes while keeping the dropdown available for direction changes', async () => {
	vi.useFakeTimers();
	const submit = vi.fn();
	const MobileControls = () => {
		const [data, setData] = useState({ sort: 'full-name', sortDir: 'asc' });
		const form = {
			data,
			setData: (name, value) => setData(previousData => ({ ...previousData, [name]: value })),
			submit,
		};

		return (
			<IndexTableControls
				filters={[]}
				bulkEdit={{ ...makeBulkEdit(), isActiveMobile: false, action: false }}
				onToggleFilters={vi.fn()}
				sorts={sorts}
				form={form}
				tableActions={[]}
				filterLabel="singers"
			/>
		);
	};
	render(<MobileControls />);
	const user = userEvent.setup({ advanceTimers: vi.advanceTimersByTime });

	await user.click(screen.getByRole('button', { name: 'Sort options' }));
	await user.click(screen.getByRole('radio', { name: 'Status' }));
	await user.click(screen.getByRole('button', { name: 'Descending' }));
	await vi.advanceTimersByTimeAsync(300);

	expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true });
	expect(screen.getByRole('button', { name: 'Sort options' })).toBeTruthy();
	expect(screen.getByText('Sort by')).toBeTruthy();
	vi.useRealTimers();
});