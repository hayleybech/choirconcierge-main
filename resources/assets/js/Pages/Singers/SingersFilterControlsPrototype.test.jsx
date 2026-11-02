import React, { useState } from 'react';
import { fireEvent, render, screen, waitFor, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { vi } from 'vitest';
import SingersFilterControlsPrototype from './SingersFilterControlsPrototype';

const { popperCalls } = vi.hoisted(() => ({ popperCalls: [] }));

vi.mock('popper.js', () => ({
	default: vi.fn((reference, popper, options) => {
		popperCalls.push({ reference, popper, options });

		return { destroy: vi.fn() };
	}),
}));

vi.mock('../../components/inputs/ButtonV2', async () => {
	const React = await vi.importActual('react');

	return {
		default: React.forwardRef(({ children, href, intent, external: _external, ...props }, ref) =>
			React.createElement(href ? 'a' : 'button', { ...props, href, ref, 'data-intent': intent }, children)
		),
	};
});

vi.mock('../../components/Icon', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({ icon, ...props }) => {
			delete props.mr;

			return React.createElement('i', { ...props, 'data-icon': icon });
		},
	};
});

const filters = [
	{
		name: 'user.name',
		label: 'Name',
		type: 'text',
		defaultValue: '',
	},
	{
		name: 'status',
		label: 'Status',
		multiple: true,
		defaultValue: ['members'],
		options: [
			{ id: 'members', name: 'Members' },
			{ id: 'prospects', name: 'Prospects' },
		],
	},
	{
		name: 'enrolments.voice_part_id',
		label: 'Voice Part',
		multiple: true,
		defaultValue: [],
		options: [
			{ id: 'soprano', name: 'Soprano', colour: 'rose' },
			{ id: 'alto', name: 'Alto', colour: 'blue' },
		],
	},
	{
		name: 'fee_status',
		label: 'Fee Status',
		multiple: true,
		defaultValue: [],
		options: [
			{ id: 'paid', name: 'Paid' },
			{ id: 'expires-soon', name: 'Expires Soon' },
			{ id: 'expired', name: 'Expired' },
			{ id: 'unknown', name: 'Unknown' },
		],
	},
	{
		name: 'enrolments.ensemble_id',
		label: 'Ensemble',
		multiple: true,
		defaultValue: [],
		options: [
			{ id: 'adult', name: 'Adult' },
			{ id: 'youth', name: 'Youth' },
		],
	},
];

const renderControls = (initialData, sorts = [], tableActions = []) => {
	const submit = vi.fn();
	const setData = vi.fn();

	const Controls = () => {
		const [data, updateData] = useState(initialData);
		const updateFormData = (name, value) => {
			setData(name, value);
			updateData(previousData => ({ ...previousData, [name]: value }));
		};

		return (
			<SingersFilterControlsPrototype
				filters={filters}
				sorts={sorts}
				form={{ data, setData: updateFormData, submit }}
				tableActions={tableActions}
			/>
		);
	};

	return { Controls, setData, submit };
};

it('uses selected intent for active filter pills and matches import/export height to sort', () => {
	const { Controls } = renderControls(
		{ sort: 'full-name', sortDir: 'asc', status: ['prospects'], 'enrolments.voice_part_id': [], fee_status: '' },
		[
			{ id: 'full-name', name: 'Name', default: true },
			{ id: 'status-title', name: 'Status' },
		],
		[
			{ label: 'Import', icon: 'file-import', onClick: vi.fn() },
			{ label: 'Export', icon: 'file-export', url: '/singers/export', download: true },
		]
	);
	render(<Controls />);

	const activeFilter = screen.getByRole('button', { name: 'Status: Prospects; open filter values' });
	const sortButton = screen.getByRole('button', { name: 'Sort: Name' });
	const importButton = screen.getByRole('button', { name: 'Import' });
	const exportButton = screen.getByRole('link', { name: 'Export' });

	expect(activeFilter.getAttribute('data-intent')).toBe('selected');
	expect(sortButton.classList.contains('h-8')).toBe(true);
	expect(importButton.classList.contains('h-8')).toBe(true);
	expect(exportButton.classList.contains('h-8')).toBe(true);
});

it('keeps a cleared status filter at Any after the delayed submit', async () => {
	const { Controls, setData, submit } = renderControls({ status: ['members'], fee_status: '' });
	render(<Controls />);

	fireEvent.click(screen.getByRole('button', { name: 'Status: Members; open filter values' }));
	fireEvent.click(screen.getByRole('button', { name: 'Clear selected' }));

	expect(screen.getByRole('button', { name: 'Status: Any; open filter values' })).toBeTruthy();
	expect(screen.getByText('Status:')).toBeTruthy();
	await waitFor(() => expect(submit).toHaveBeenCalledTimes(1));
	expect(setData).toHaveBeenCalledWith('status', ['']);
});

it('keeps Add Filter open while staging a value, then applies it from the second-level panel', async () => {
	popperCalls.length = 0;
	const { Controls, setData } = renderControls({ status: ['members'], fee_status: '' });
	render(<Controls />);
	const user = userEvent.setup();

	const addFilterButton = screen.getByRole('button', { name: 'Add filter' });
	await user.click(addFilterButton);
	expect(screen.getByRole('menu')).toBeTruthy();
	const menuPanel = screen.getByRole('menu');
	const menuPosition = popperCalls.find(call => call.popper === menuPanel);
	expect(menuPosition.reference).toBe(addFilterButton);
	expect(menuPosition.options).toMatchObject({ placement: 'bottom-start', positionFixed: true });
	await user.click(screen.getByRole('menuitem', { name: 'Fee Status' }));

	expect(screen.getByRole('button', { name: 'Add filter' })).toBeTruthy();
	expect(screen.getByRole('menu')).toBeTruthy();
	expect(
		screen.getByRole('group', { name: 'Fee Status values' }).classList.contains('fixed')
	).toBe(true);
	expect(screen.getByRole('menuitem', { name: 'Fee Status' })).toBeTruthy();
	expect(screen.getByRole('menuitem', { name: 'Apply' }).getAttribute('data-intent')).toBe('secondary');
	expect(screen.getByRole('menuitem', { name: 'Apply' }).querySelector('[data-icon="fa-check"]')).toBeTruthy();

	const paidCheckbox = screen.getByLabelText('Paid');
	expect(paidCheckbox.closest('label').querySelector('[data-icon="check-circle"]').classList.contains('text-green-500')).toBe(true);
	await user.click(paidCheckbox);
	expect(screen.getByRole('menu')).toBeTruthy();
	expect(paidCheckbox).toHaveProperty('checked', true);
	expect(screen.getByRole('menuitem', { name: 'Apply' }).getAttribute('data-intent')).toBe('primary');
	expect(setData).not.toHaveBeenCalledWith('fee_status', ['paid']);
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(setData).toHaveBeenCalledWith('fee_status', ['paid']);
	expect(screen.getByRole('button', { name: 'Fee Status: Paid; open filter values' })).toBeTruthy();
	expect(screen.queryByRole('menu')).toBeNull();
});

it('applies multiple checkbox selections together from Add Filter', async () => {
	const { Controls, setData } = renderControls({ status: ['members'], fee_status: '', 'enrolments.ensemble_id': [] });
	render(<Controls />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Ensemble' }));
	await user.click(screen.getByLabelText('Adult'));
	await user.click(screen.getByLabelText('Youth'));

	expect(setData).not.toHaveBeenCalledWith('ensemble', ['adult', 'youth']);
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(setData).toHaveBeenCalledWith('enrolments.ensemble_id', ['adult', 'youth']);
});

it('selects all Fee Status options in the Add Filter menu', async () => {
	const { Controls, setData } = renderControls({ status: ['members'], fee_status: [] });
	render(<Controls />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Fee Status' }));
	await user.click(screen.getByText('All', { selector: 'button' }));

	['Paid', 'Expires Soon', 'Expired', 'Unknown'].forEach(option => {
		expect(screen.getByLabelText(option)).toHaveProperty('checked', true);
	});

	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(setData).toHaveBeenCalledWith('fee_status', ['paid', 'expires-soon', 'expired', 'unknown']);
	expect(screen.getByRole('button', { name: 'Fee Status: 4 selected; open filter values' })).toBeTruthy();
});

it('shows a selected count beside the filter name and places All at the far right of the popover header', async () => {
	const { Controls } = renderControls({ status: ['members'], fee_status: ['paid'] });
	render(<Controls />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Fee Status: Paid; open filter values' }));

	const heading = screen.getByRole('heading', { name: 'Fee Status' });
	const header = heading.parentElement.parentElement;
	const selectedCount = within(header).getByText('1 selected');
	const allButton = within(header).getByRole('button', { name: 'All' });

	expect(heading.parentElement.contains(selectedCount)).toBe(true);
	expect(heading.compareDocumentPosition(selectedCount) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
	expect(header.lastElementChild).toBe(allButton);
});

it('keeps Apply outside the scrollable filter options', async () => {
	const { Controls } = renderControls({ status: ['members'], fee_status: '', 'enrolments.ensemble_id': [] });
	render(<Controls />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Ensemble' }));

	const optionsScroller = screen.getByLabelText('Adult').closest('.overflow-y-auto');
	const applyButton = screen.getByRole('menuitem', { name: 'Apply' });

	expect(optionsScroller).toBeTruthy();
	expect(optionsScroller.contains(applyButton)).toBe(false);
});

it('keeps the sort popover open while changing sort and direction', async () => {
	popperCalls.length = 0;
	const sorts = [
		{ id: 'full-name', name: 'First Name' },
		{ id: 'status-title', name: 'Status' },
	];
	const { Controls, setData, submit } = renderControls({ sort: 'full-name', sortDir: 'asc' }, sorts);
	render(<Controls />);
	const user = userEvent.setup();

	const sortButton = screen.getByRole('button', { name: 'Sort: First Name' });
	await user.click(sortButton);
	const sortPanel = screen.getByText('Sort by').parentElement;
	const sortPosition = popperCalls.find(call => call.reference === sortButton);
	expect(document.body.contains(sortPanel)).toBe(true);
	expect(sortPanel.classList.contains('z-50')).toBe(true);
	expect(sortPosition.options).toMatchObject({ placement: 'bottom-end', positionFixed: true });
	await user.click(screen.getByRole('radio', { name: 'Status' }));
	expect(screen.getByText('Sort by')).toBeTruthy();

	await user.click(screen.getByRole('button', { name: 'Descending' }));
	expect(screen.getByText('Sort by')).toBeTruthy();
	expect(setData).toHaveBeenCalledWith('sort', 'status-title');
	expect(setData).toHaveBeenCalledWith('sortDir', 'desc');
	await waitFor(() => expect(submit).toHaveBeenCalledWith(undefined, { preserveState: true }));
});

it('shows name search before filter pills and excludes it from Add Filter', () => {
	const { Controls, setData } = renderControls({ 'user.name': 'Ada', status: ['members'], fee_status: '' });
	const { container } = render(<Controls />);

	const nameSearch = screen.getByRole('searchbox', { name: 'Search singers by name' });
	const statusPill = screen.getByRole('button', { name: 'Status: Members; open filter values' });

	expect(screen.getByDisplayValue('Ada')).toBe(nameSearch);
	expect(nameSearch.compareDocumentPosition(statusPill) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
	expect(nameSearch.classList.contains('h-[26px]')).toBe(true);
	expect(nameSearch.closest('label').classList.contains('w-40')).toBe(true);
	expect(nameSearch.classList.contains('shadow-none')).toBe(true);
	expect(container.querySelector('[data-icon="search"]')).toBeTruthy();

	fireEvent.click(screen.getByRole('button', { name: 'Add filter' }));
	expect(screen.queryByRole('button', { name: 'Name' })).toBeNull();

	fireEvent.change(nameSearch, { target: { value: 'Alex' } });
	expect(setData).toHaveBeenCalledWith('user.name', 'Alex');
	fireEvent.click(screen.getByRole('button', { name: 'Clear singer name search' }));
	expect(setData).toHaveBeenCalledWith('user.name', '');
});

it('renders singer filter values with the site styles in pills and option lists', async () => {
	const { Controls } = renderControls({
		status: ['members'],
		'enrolments.voice_part_id': ['soprano'],
		fee_status: ['paid'],
		'enrolments.ensemble_id': ['adult'],
	});
	render(<Controls />);
	const user = userEvent.setup();

	const statusPill = screen.getByRole('button', { name: 'Status: Members; open filter values' });
	expect(statusPill.querySelector('[data-icon="circle"]').classList.contains('text-emerald-500')).toBe(true);
	expect(statusPill.querySelector('[data-icon="circle"]').classList.contains('text-[10px]')).toBe(true);
	expect(Array.from(statusPill.querySelectorAll('span')).some(element => element.textContent === 'Members' && element.classList.contains('text-xs'))).toBe(true);

	const voicePartPill = screen.getByRole('button', { name: 'Voice Part: Soprano; open filter values' });
	const voicePartBadge = Array.from(voicePartPill.querySelectorAll('span')).find(element => element.textContent === 'Soprano');
	expect(voicePartBadge.classList.contains('bg-rose-500/75')).toBe(true);
	expect(voicePartBadge.classList.contains('px-1.5')).toBe(true);
	expect(voicePartBadge.querySelector('svg').classList.contains('text-white')).toBe(true);

	const ensemblePill = screen.getByRole('button', { name: 'Ensemble: Adult; open filter values' });
	const ensembleBadge = Array.from(ensemblePill.querySelectorAll('span')).find(element => element.textContent === 'Adult');
	expect(ensembleBadge.classList.contains('bg-purple-100')).toBe(true);
	expect(ensembleBadge.classList.contains('px-1')).toBe(true);

	const feeStatusPill = screen.getByRole('button', { name: 'Fee Status: Paid; open filter values' });
	expect(feeStatusPill.querySelector('[data-icon="check-circle"]').classList.contains('text-green-500')).toBe(true);
	expect(feeStatusPill.querySelector('[data-icon="check-circle"]').classList.contains('text-xs')).toBe(true);

	await user.click(statusPill);
	const prospectOption = screen.getByLabelText('Prospects');
	expect(prospectOption.closest('label').querySelector('[data-icon="circle"]').classList.contains('text-amber-500')).toBe(true);

	await user.click(voicePartPill);
	const altoOption = screen.getByLabelText('Alto');
	const altoBadge = altoOption.closest('label').querySelector('span');
	expect(altoBadge.classList.contains('bg-blue-500/75')).toBe(true);
	expect(altoBadge.querySelector('svg').classList.contains('text-white')).toBe(true);

	await user.click(ensemblePill);
	const youthOption = screen.getByLabelText('Youth');
	expect(youthOption.closest('label').querySelector('span').classList.contains('bg-purple-100')).toBe(true);

	await user.click(feeStatusPill);
	const expiredOption = screen.getByLabelText('Expired');
	expect(expiredOption.closest('label').querySelector('[data-icon="times-circle"]').classList.contains('text-red-500')).toBe(true);
});

it('places search and Add Filter above a horizontally scrollable pill row in the final layout', () => {
	const { Controls } = renderControls({ 'user.name': '', status: ['members'], fee_status: '' });
	render(<Controls />);

	const searchRow = screen.getByRole('searchbox', { name: 'Search singers by name' }).closest('.items-center');
	const addFilterButton = screen.getByRole('button', { name: 'Add filter' });
	const pillRow = screen.getByRole('button', { name: 'Status: Members; open filter values' }).closest('.overflow-x-auto');

	expect(searchRow.contains(addFilterButton)).toBe(true);
	expect(pillRow).toBeTruthy();
	expect(searchRow.compareDocumentPosition(pillRow) & Node.DOCUMENT_POSITION_FOLLOWING).not.toBe(0);
});

it('locks the controls to layout C and places Add Filter after the pills on desktop', () => {
	const { Controls } = renderControls({ 'user.name': '', status: ['members'], fee_status: '' });
	const { container } = render(<Controls />);

	const addFilterButton = screen.getByRole('button', { name: 'Add filter' });
	const addFilterContainer = addFilterButton.parentElement.parentElement;
	const pillRow = screen.getByRole('button', { name: 'Status: Members; open filter values' }).closest('.overflow-x-auto');

	expect(addFilterContainer.classList.contains('lg:order-3')).toBe(true);
	expect(pillRow.classList.contains('lg:contents')).toBe(true);
	expect(container.querySelector('[aria-label="Next prototype layout"]')).toBeNull();
});

it('clears every singer filter while preserving an empty override for defaulted filters', async () => {
	const { Controls, setData } = renderControls({
		'user.name': 'Ada',
		status: ['prospects'],
		'enrolments.voice_part_id': ['alto'],
		fee_status: ['paid'],
		'enrolments.ensemble_id': ['adult'],
	});
	render(<Controls />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Clear all filters' }));

	expect(setData).toHaveBeenCalledWith('user.name', '');
	expect(setData).toHaveBeenCalledWith('status', ['']);
	expect(setData).toHaveBeenCalledWith('enrolments.voice_part_id', []);
	expect(setData).toHaveBeenCalledWith('fee_status', []);
	expect(setData).toHaveBeenCalledWith('enrolments.ensemble_id', []);
	expect(screen.getByRole('button', { name: 'Status: Any; open filter values' })).toBeTruthy();
	expect(screen.queryByRole('button', { name: 'Voice Part: Alto; open filter values' })).toBeNull();
	expect(screen.queryByRole('button', { name: 'Fee Status: Paid; open filter values' })).toBeNull();
});

it('restores default values for every singer filter', async () => {
	const { Controls, setData } = renderControls({
		'user.name': 'Ada',
		status: ['prospects'],
		'enrolments.voice_part_id': ['alto'],
		fee_status: ['paid'],
		'enrolments.ensemble_id': ['adult'],
	});
	render(<Controls />);
	const user = userEvent.setup();

	await user.click(screen.getByRole('button', { name: 'Reset to default' }));

	expect(setData).toHaveBeenCalledWith('user.name', '');
	expect(setData).toHaveBeenCalledWith('status', ['members']);
	expect(setData).toHaveBeenCalledWith('enrolments.voice_part_id', []);
	expect(setData).toHaveBeenCalledWith('fee_status', []);
	expect(setData).toHaveBeenCalledWith('enrolments.ensemble_id', []);
	expect(screen.getByRole('button', { name: 'Status: Members; open filter values' })).toBeTruthy();
	expect(screen.queryByRole('button', { name: 'Voice Part: Alto; open filter values' })).toBeNull();
});

it('portals filter dropdowns above the mobile header and configures viewport-aware placement', async () => {
	popperCalls.length = 0;
	const { Controls } = renderControls({ status: ['members'], fee_status: '' });
	render(<Controls />);
	const user = userEvent.setup();
	const statusPill = screen.getByRole('button', { name: 'Status: Members; open filter values' });

	await user.click(statusPill);

	const filterPanel = screen.getByText('Clear selected').closest('.z-50');
	const filterPosition = popperCalls.find(call => call.reference === statusPill);

	expect(filterPanel).toBeTruthy();
	expect(document.body.contains(filterPanel)).toBe(true);
	expect(filterPosition.options).toMatchObject({
		placement: 'bottom-start',
		positionFixed: true,
		modifiers: {
			flip: { boundariesElement: 'viewport' },
			preventOverflow: { boundariesElement: 'viewport' },
		},
	});

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Fee Status' }));

	expect(popperCalls.some(call => call.options.placement === 'right-start')).toBe(true);
});