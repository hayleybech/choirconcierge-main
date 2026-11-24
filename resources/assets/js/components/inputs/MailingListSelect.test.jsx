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

it('sizes drawer chips to their content and constrains them to the drawer', () => {
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
	const chipLabel = control.querySelector('.mailing-list-select__multi-value__label');
	const valueContainer = control.querySelector('.mailing-list-select__value-container');
	const wrapper = control.closest('.mt-1');

	expect(wrapper).toHaveClass('w-full', 'min-w-0', 'max-w-full');
	expect(control.parentElement).toHaveClass('w-full', 'min-w-0', 'max-w-full');
	expect(valueContainer).toHaveStyle({ flex: '1 1 0%', flexWrap: 'wrap', maxWidth: '100%' });
	expect(chips).toHaveLength(2);
	chips.forEach(chip => expect(chip).toHaveStyle({ flex: '0 1 auto', maxWidth: 'calc(100% - 4px)' }));
	expect(chipLabel).toHaveStyle({ flex: '1 1 auto', maxWidth: '100%' });
	expect(listName).toHaveClass('min-w-0', 'max-w-full', 'flex-1', 'truncate');
	expect(listName).toHaveAttribute('title', longOption.label);
});

it('shows a single selected mailing list as plain inline text beside To', () => {
	renderToolbarSelect([options[0]]);
	const control = screen.getByRole('combobox', { name: 'To' }).closest('.mailing-list-select__control');

	expect(within(control).getByText('To:')).toHaveClass('text-xs');
	const selectedValue = within(control).getByText('Announcements').parentElement;
	expect(selectedValue).toHaveClass('text-xs', 'gap-0.5');
	expect(within(selectedValue).getByText('Announcements')).toHaveClass('max-w-[20ch]', 'truncate');
	expect(selectedValue.querySelector('i')).not.toHaveClass('mr-1.5');
	expect(control.querySelector('.mailing-list-select__multi-value')).not.toBeInTheDocument();
	expect(control.querySelector('.mailing-list-select__multi-value__remove')).not.toBeInTheDocument();
	expect(control.closest('.w-auto')).toBeInTheDocument();
});

it('summarizes multiple mailing lists inline and shows selected items with remove actions above other options', async () => {
	const user = userEvent.setup();
	const updateFn = vi.fn();
	renderToolbarSelect(options.slice(0, 2), updateFn);
	const control = screen.getByRole('combobox', { name: 'To' }).closest('.mailing-list-select__control');

	expect(within(control).getByText('To:')).toHaveClass('text-xs');
	expect(within(control).getByText('2 selected')).toHaveClass('text-xs');
	expect(control.querySelector('.mailing-list-select__multi-value')).not.toBeInTheDocument();

	await user.click(screen.getByRole('combobox', { name: 'To' }));

	const menuList = screen.getByRole('listbox');
	const menuPortal = document.body.querySelector('.mailing-list-select__menu-portal');
	const menu = menuPortal.querySelector('.mailing-list-select__menu');
	const selectedItems = within(menuList).getByRole('group', { name: 'Selected mailing lists' });
	const optionsList = within(menuList).getByRole('option', { name: /Board/ });

	expect(menuPortal.parentElement).toBe(document.body);
	expect(menuPortal).toHaveStyle({ zIndex: '50' });
	expect(menu).toHaveStyle({ minWidth: '250px' });
	expect(selectedItems.compareDocumentPosition(optionsList) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy();
	expect(within(selectedItems).getByText('Announcements')).toBeInTheDocument();
	expect(within(selectedItems).queryByText('announcements@example.com')).not.toBeInTheDocument();
	expect(
		within(selectedItems)
			.getByText('Announcements')
			.closest('div')
	).toHaveClass('text-xs');
	expect(optionsList.querySelector('div')).toHaveClass('text-xs');
	expect(optionsList.querySelector('i')).toHaveClass('text-xs');

	const removeButton = within(selectedItems).getByRole('button', { name: 'Remove Announcements' });
	expect(removeButton.querySelector('i')).toHaveClass('far', 'text-xs');
	await user.click(removeButton);

	expect(updateFn).toHaveBeenLastCalledWith([options[1].value]);
});

it('matches a selected filter pill in color, spacing, typography, and height', () => {
	renderToolbarSelect([options[0]]);
	const control = screen.getByRole('combobox', { name: 'To' }).closest('.mailing-list-select__control');
	const valueContainer = control.querySelector('.mailing-list-select__value-container');
	const clearIcon = control.querySelector('.fa-times');
	const chevronIcon = control.querySelector('.fa-chevron-down');

	expect(control).toHaveStyle({
		backgroundColor: '#faf5ff',
		borderColor: '#e9d5ff',
		height: '26px',
	});
	expect(valueContainer).toHaveStyle({ padding: '0px 6px' });
	expect(within(control).getByText('To:')).toHaveClass('text-xs', 'text-purple-700');
	expect(within(control).getByText('Announcements').parentElement).toHaveClass('text-xs', 'text-purple-700');
	expect(clearIcon).toHaveClass('far', 'text-xs', 'text-purple-600');
	expect(chevronIcon).toHaveClass('far', 'text-xs', 'text-purple-600');
	expect(control).toHaveStyle({ borderRadius: '0.25rem' });
	expect(control.querySelector('.mailing-list-select__clear-indicator')).toHaveStyle({ padding: '0px 4px' });
	expect(control.querySelector('.mailing-list-select__dropdown-indicator')).toHaveStyle({ padding: '0px 4px' });
});

it('uses the secondary filter-pill colors when no mailing list is selected', () => {
	renderToolbarSelect([]);
	const control = screen.getByRole('combobox', { name: 'To' }).closest('.mailing-list-select__control');
	const chevronIcon = control.querySelector('.fa-chevron-down');

	expect(control).toHaveStyle({ backgroundColor: '#ffffff', borderColor: '#e5e7eb', height: '26px' });
	expect(within(control).getByText('To:')).toHaveClass('text-xs', 'text-gray-600');
	expect(chevronIcon).toHaveClass('text-gray-500');
});
