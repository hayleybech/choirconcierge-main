import React, { useState } from 'react';
import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import SingerFilters from './SingerFilters';

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

const renderSingerFilters = () => {
	const submit = vi.fn();
	const setData = vi.fn();

	const FilterForm = () => {
		const [data, updateData] = useState({
			'user.name': '',
			status: ['members'],
			'enrolments.voice_part_id': [],
			'roles.id': [],
			fee_status: [],
			'enrolments.ensemble_id': [],
		});

		return (
			<SingerFilters
				statuses={statuses}
				defaultStatus="members"
			voiceParts={voiceParts}
			roles={roles}
			ensembles={ensembles}
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
	expect(statusField.querySelector('legend')).toHaveClass('text-purple-700');
	expect(statusField.querySelector('legend')).toHaveTextContent('*');
	expect(screen.getByLabelText(/Prospects/).labels[0]).not.toHaveClass('text-purple-700');
	expect(screen.getByLabelText(/Prospects/).labels[0]).toHaveTextContent('*');
	expect(within(statusField).getByRole('button', { name: 'Reset Status filter' })).toBeEnabled();
	expect(within(statusField).getByRole('button', { name: 'None' })).toBeEnabled();
	expect(within(statusField).getByRole('button', { name: 'Reset Status filter' })).toHaveClass(
		'disabled:text-gray-400'
	);

	await user.click(within(statusField).getByRole('button', { name: 'None' }));
	expect(setData).toHaveBeenCalledWith('status', ['']);
});

it('does not mark default filter names with an asterisk', () => {
	renderSingerFilters();

	expect(screen.queryByText('*')).not.toBeInTheDocument();
});

it('uses the site styles for filter values and marks non-default values with an asterisk', async () => {
	renderSingerFilters();
	const user = userEvent.setup();

	const memberLabel = screen.getByLabelText('Members').labels[0];
	expect(memberLabel.querySelector('.fa-circle')).toHaveClass('text-emerald-500');

	const voicePartLabel = screen.getByLabelText('Soprano').labels[0];
	const voicePartTag = Array.from(voicePartLabel.querySelectorAll('span')).find(span =>
		span.classList.contains('bg-rose-500/75')
	);
	expect(voicePartTag).toBeTruthy();
	expect(voicePartTag.querySelector('svg')).toHaveClass('text-white');

	const ensembleLabel = screen.getByLabelText('Adult').labels[0];
	expect(ensembleLabel.querySelector('.bg-purple-100')).toBeTruthy();

	const roleLabel = screen.getByLabelText('Admin').labels[0];
	expect(roleLabel.querySelector('.bg-gray-100')).toBeTruthy();

	const paidOption = screen.getByRole('checkbox', { name: /Paid/ });
	expect(paidOption.labels[0].querySelector('.fa-check-circle')).toHaveClass('text-green-500');

	await user.click(screen.getByLabelText(/Prospects/));
	const prospectsLabel = screen.getByLabelText(/Prospects/).labels[0];
	expect(prospectsLabel).toHaveTextContent('*');
	expect(prospectsLabel.querySelector('.fa-circle')).toHaveClass('text-amber-500');
	expect(prospectsLabel).not.toHaveClass('text-purple-700');

	await user.click(screen.getByLabelText(/Members/));
	expect(screen.getByLabelText(/Members/).labels[0]).toHaveTextContent('*');

	await user.click(screen.getByRole('checkbox', { name: /Expired/ }));
	const expiredOption = screen.getByRole('checkbox', { name: /Expired/ });
	expect(expiredOption.labels[0]).toHaveTextContent('*');
	expect(expiredOption.labels[0].querySelector('.fa-times-circle')).toHaveClass('text-red-500');
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

	await user.click(within(feeStatusField).getByRole('button', { name: 'All' }));
	feeStatusOptions.forEach(option => {
		expect(within(feeStatusField).getByRole('checkbox', { name: new RegExp(option) })).toBeChecked();
	});
	expect(setData).toHaveBeenLastCalledWith('fee_status', ['paid', 'expires-soon', 'expired', 'unknown']);
});

it('shows the fee status selection count next to its filter name', async () => {
	renderSingerFilters();
	const user = userEvent.setup();
	const feeStatusField = screen.getByText('Fee Status').closest('fieldset');
	const selectedCount = feeStatusField.querySelector('legend .text-xs');

	expect(selectedCount).toHaveTextContent('0 selected');
	await user.click(within(feeStatusField).getByRole('checkbox', { name: /Paid/ }));
	expect(selectedCount).toHaveTextContent('1 selected');
	expect(within(feeStatusField).getByRole('button', { name: 'All' })).toBeInTheDocument();
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
