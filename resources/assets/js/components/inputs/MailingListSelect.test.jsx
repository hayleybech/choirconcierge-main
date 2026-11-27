import React from 'react';
import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import MailingListSelect from './MailingListSelect';

const options = [
	{
		label: 'Announcements',
		value: 'announcements@example.com',
		email: 'announcements@example.com',
		type_icon: 'users',
	},
	{ label: 'Choir', value: 'choir@example.com', email: 'choir@example.com', type_icon: 'users' },
	{ label: 'Board', value: 'board@example.com', email: 'board@example.com', type_icon: 'users' },
];

const renderToolbarSelect = (value, updateFn = vi.fn()) =>
	render(
		<MailingListSelect
			options={options}
			value={value}
			updateFn={updateFn}
			multiple
			isClearable
			prefixLabel="To:"
			ariaLabel="To"
			toolbarPill
			className="w-auto shrink-0"
		/>
	);

it('renders selected mailing list chips and preserves their full labels', () => {
	const longOption = {
		label: 'Very Long Rehearsal Mailing List Name',
		value: 'long-list@example.com',
		email: 'long-list@example.com',
		type_icon: 'users',
	};
	render(
		<MailingListSelect
			options={[longOption, options[1]]}
			value={[longOption, options[1]]}
			updateFn={vi.fn()}
			multiple
			constrainWidth
			truncateSelectedLabels
			className="mt-1 mb-3 w-full min-w-0 max-w-full"
		/>
	);

	const control = screen.getByRole('combobox').closest('.mailing-list-select__control');
	const listName = screen.getByText(longOption.label);
	const chips = control.querySelectorAll('.mailing-list-select__multi-value');

	expect(chips).toHaveLength(2);
	expect(listName).toHaveAttribute('title', longOption.label);
});

it('shows a single selected mailing list as plain inline text beside To', () => {
	renderToolbarSelect([options[0]]);
	const control = screen.getByRole('combobox', { name: 'To' }).closest('.mailing-list-select__control');

	expect(within(control).getByText('To:')).toBeInTheDocument();
	expect(within(control).getByText('Announcements')).toBeInTheDocument();
	expect(control.querySelector('.mailing-list-select__multi-value')).not.toBeInTheDocument();
	expect(control.querySelector('.mailing-list-select__multi-value__remove')).not.toBeInTheDocument();
});

it('summarizes multiple mailing lists inline and shows selected items with remove actions above other options', async () => {
	const user = userEvent.setup();
	const updateFn = vi.fn();
	renderToolbarSelect(options.slice(0, 2), updateFn);
	const control = screen.getByRole('combobox', { name: 'To' }).closest('.mailing-list-select__control');

	expect(within(control).getByText('To:')).toBeInTheDocument();
	expect(within(control).getByText('2 selected')).toBeInTheDocument();
	expect(control.querySelector('.mailing-list-select__multi-value')).not.toBeInTheDocument();

	await user.click(screen.getByRole('combobox', { name: 'To' }));

	const menuList = screen.getByRole('listbox');
	const menuPortal = document.body.querySelector('.mailing-list-select__menu-portal');
	const selectedItems = within(menuList).getByRole('group', { name: 'Selected mailing lists' });
	const optionsList = within(menuList).getByRole('option', { name: /Board/ });

	expect(menuPortal.parentElement).toBe(document.body);
	expect(selectedItems.compareDocumentPosition(optionsList) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy();
	expect(within(selectedItems).getByText('Announcements')).toBeInTheDocument();
	expect(within(selectedItems).queryByText('announcements@example.com')).not.toBeInTheDocument();
	const removeButton = within(selectedItems).getByRole('button', { name: 'Remove Announcements' });
	await user.click(removeButton);

	expect(updateFn).toHaveBeenLastCalledWith([options[1].value]);
});
