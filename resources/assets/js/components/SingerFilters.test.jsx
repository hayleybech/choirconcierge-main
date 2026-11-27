import React, { useState } from 'react';
import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import Filters from './Filters';
import { createSingerIndexFilterDescriptors } from './SingerFilterDescriptors';

vi.mock('../hooks/useRoute', () => ({
	default: () => ({ route: () => '/singers' }),
}));

const statuses = [
	{ id: 'members', name: 'Members' },
	{ id: 'prospects', name: 'Prospects' },
];
const voiceParts = [{ id: 'soprano', title: 'Soprano', colour: 'rose' }];
const roles = [{ id: 'admin', name: 'Admin' }];
const ensembles = [
	{ id: 'adult', name: 'Adult' },
	{ id: 'youth', name: 'Youth' },
];
const filters = createSingerIndexFilterDescriptors({
	statuses,
	defaultStatus: 'members',
	voiceParts,
	roles,
	ensembles,
});

const renderSingerFilters = () => {
	const submit = vi.fn();
	const setData = vi.fn();

	const FilterForm = () => {
		const [data, updateData] = useState({
			'user.name': '',
			'status': ['members'],
			'enrolments.voice_part_id': [],
			'roles.id': [],
			'fee_status': [],
			'enrolments.ensemble_id': [],
		});

		return (
			<Filters
				routeName="singers.index"
				filters={filters}
				autoSubmit={false}
				showClearAll={false}
				form={{
					data,
					setData: (name, value) => {
						setData(name, value);
						updateData(previousData => ({ ...previousData, [name]: value }));
					},
					submit,
				}}
			/>
		);
	};

	return { submit, setData, ...render(<FilterForm />) };
};

it('stages filter edits until Apply and marks changed fields and values', async () => {
	const { submit, setData } = renderSingerFilters();
	const user = userEvent.setup();
	const statusField = screen.getByText('Status').closest('fieldset');

	expect(within(statusField).getByRole('button', { name: 'Reset Status filter' })).toBeDisabled();
	await user.click(screen.getByLabelText(/Prospects/));

	expect(submit).not.toHaveBeenCalled();
	expect(statusField.querySelector('legend')).toHaveTextContent('Status');
	expect(within(statusField).getAllByTitle('Filter differs from default').length).toBeGreaterThan(0);
	expect(within(statusField).getByRole('button', { name: 'Reset Status filter' })).toBeEnabled();
	expect(within(statusField).getByRole('button', { name: 'None' })).toBeEnabled();

	await user.click(within(statusField).getByRole('button', { name: 'None' }));
	expect(setData).toHaveBeenCalledWith('status', ['']);
});

it('does not mark default filter names with an asterisk', () => {
	renderSingerFilters();

	expect(screen.queryByText('*')).not.toBeInTheDocument();
});

it('keeps the Singer drawer ensemble field directly after voice part', () => {
	const { container } = renderSingerFilters();
	const filterLabels = [...container.querySelectorAll('fieldset legend')].map(legend => legend.textContent);

	expect(filterLabels).toEqual(['Status', 'Voice Part', 'Ensemble', 'Role', 'Fee Status']);
});

it('marks the fee-status field as changed when a non-default status is selected', async () => {
	renderSingerFilters();
	const user = userEvent.setup();

	await user.click(screen.getByRole('checkbox', { name: /Expired/ }));
	const feeStatusField = screen.getByText('Fee Status').closest('fieldset');
	expect(within(feeStatusField).getByTitle('Filter differs from default')).toBeInTheDocument();
});

it('supports selecting multiple fee statuses and selecting all of them', async () => {
	const { setData } = renderSingerFilters();
	const user = userEvent.setup();
	const feeStatusField = screen.getByText('Fee Status').closest('fieldset');
	const feeStatusOptions = ['Paid', 'Expires Soon', 'Expired', 'Unknown'];

	feeStatusOptions.forEach(option => {
		expect(within(feeStatusField).getByRole('checkbox', { name: new RegExp(option) })).not.toBeChecked();
	});

	await user.click(within(feeStatusField).getByRole('checkbox', { name: /Expired/ }));
	await user.click(within(feeStatusField).getByRole('checkbox', { name: /Unknown/ }));

	expect(within(feeStatusField).getByRole('checkbox', { name: /Expired/ })).toBeChecked();
	expect(within(feeStatusField).getByRole('checkbox', { name: /Unknown/ })).toBeChecked();
	expect(setData).toHaveBeenLastCalledWith('fee_status', ['expired', 'unknown']);

	await user.click(within(feeStatusField).getByRole('button', { name: 'Select All' }));
	feeStatusOptions.forEach(option => {
		expect(within(feeStatusField).getByRole('checkbox', { name: new RegExp(option) })).toBeChecked();
	});
	expect(setData).toHaveBeenLastCalledWith('fee_status', ['paid', 'expires-soon', 'expired', 'unknown']);
});

it('shows the fee status selection count next to its filter name', async () => {
	renderSingerFilters();
	const user = userEvent.setup();
	const feeStatusField = screen.getByText('Fee Status').closest('fieldset');
	const selectedCount = within(feeStatusField).getByText('0 selected');

	expect(selectedCount).toHaveTextContent('0 selected');
	await user.click(within(feeStatusField).getByRole('checkbox', { name: /Paid/ }));
	expect(selectedCount).toHaveTextContent('1 selected');
	expect(within(feeStatusField).getByRole('button', { name: 'Select All' })).toBeInTheDocument();
});

it('restores a field default with Reset without submitting the form', async () => {
	const { submit } = renderSingerFilters();
	const user = userEvent.setup();
	const statusField = screen.getByText('Status').closest('fieldset');

	await user.click(screen.getByLabelText(/Prospects/));
	await user.click(within(statusField).getByRole('button', { name: 'Reset Status filter' }));

	expect(screen.getByLabelText('Members')).toBeChecked();
	expect(screen.getByLabelText(/Prospects/)).not.toBeChecked();
	expect(within(statusField).getByRole('button', { name: 'Reset Status filter' })).toBeDisabled();
	expect(submit).not.toHaveBeenCalled();
});
