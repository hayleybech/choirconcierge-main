import React, { useState } from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import MailLogFilters from './MailLogFilters';
import IndexTableControls from '../../../components/IndexTableControls';
import { createMailingListMailLogFilterDescriptors } from './MailLogFilterDescriptors';
import { getDefaultFilterData } from '../../../components/filterDescriptorUtils';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn(() => false) }));

vi.mock('react-responsive', () => ({
	useMediaQuery: mockUseMediaQuery,
}));

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: () => '/filters' }),
}));

vi.mock('../../../components/inputs/MailingListSelect', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({
			options,
			updateFn,
			isClearable = true,
			prefixLabel,
			toolbarPill = false,
			constrainWidth = false,
			truncateSelectedLabels = false,
			className = 'mt-1',
		}) =>
			React.createElement(
				'div',
				{
					'data-testid': 'mailing-list-select',
					'data-toolbar-pill': String(toolbarPill),
					'data-constrain-width': String(constrainWidth),
					'data-truncate-selected-labels': String(truncateSelectedLabels),
					'data-class-name': className,
				},
				prefixLabel && React.createElement('span', null, prefixLabel),
				React.createElement(
					'button',
					{ type: 'button', onClick: () => updateFn(options.slice(0, 1).map(option => option.value)) },
					'Select first mailing list'
				),
				isClearable &&
					React.createElement(
						'button',
						{ type: 'button', onClick: () => updateFn([]) },
						'Clear selected mailing lists'
					)
			),
	};
});

const mailingLists = [{ title: 'Announcements', email: 'announcements@example.com', type_icon: 'users' }];
const mailTypes = [{ id: 'broadcast', name: 'Broadcast' }];

const MailLogFilterForm = ({ inboxEnabled }) => {
	const filters = createMailingListMailLogFilterDescriptors({
		mailingLists,
		mailTypes,
		defaultMailTypes: ['broadcast'],
		inboxEnabled,
	});
	const [data, setData] = useState({ subject: '', to: [], from: '', type: ['broadcast'], read: '' });
	const form = {
		data,
		defaultFilterData: getDefaultFilterData(filters),
		searchableFilterNames: [],
		setData: (name, value) => setData(previousData => ({ ...previousData, [name]: value })),
		submit: vi.fn(),
	};

	return <MailLogFilters form={form} filters={filters} />;
};

describe('mail log read-status filter', () => {
	it('hides read-status filters when inbox is disabled', () => {
		render(<MailLogFilterForm inboxEnabled={false} />);

		expect(screen.queryByText('Read Status')).not.toBeInTheDocument();
		expect(screen.queryByLabelText('Unread')).not.toBeInTheDocument();
	});

	it('shows read-status filters when inbox is enabled', () => {
		render(<MailLogFilterForm inboxEnabled />);

		expect(screen.getByText('Read Status')).toBeInTheDocument();
		expect(screen.getByLabelText('Unread')).toBeInTheDocument();
		expect(screen.getByRole('button', { name: 'Select first mailing list' })).toBeInTheDocument();

		fireEvent.click(screen.getByRole('button', { name: 'Select first mailing list' }));

		expect(screen.getAllByText('1 selected')).toHaveLength(2);
	});

	it('renders Communications To as a direct, clearable desktop selector', async () => {
		mockUseMediaQuery.mockReturnValue(true);
		const user = userEvent.setup();
		const filterDescriptors = createMailingListMailLogFilterDescriptors({
			mailingLists,
			mailTypes,
			defaultMailTypes: ['broadcast'],
			inboxEnabled: true,
		});
		const setData = vi.fn();

		render(
			<IndexTableControls
				filters={filterDescriptors}
				form={{
					data: { subject: '', to: ['announcements@example.com'], from: '', type: ['broadcast'], read: '' },
					setData,
					submit: vi.fn(),
				}}
				filterLabel="communications"
			/>
		);

		expect(screen.queryByRole('button', { name: /To:.*open filter values/ })).not.toBeInTheDocument();
		const mailingListSelect = screen.getByTestId('mailing-list-select');
		expect(mailingListSelect).toContainElement(screen.getByText('To:'));
		expect(mailingListSelect).toHaveAttribute('data-toolbar-pill', 'true');
		expect(mailingListSelect).toHaveAttribute('data-class-name', 'w-auto shrink-0');
		await user.click(screen.getByRole('button', { name: 'Clear selected mailing lists' }));

		expect(setData).toHaveBeenCalledWith('to', []);
	});

	it('hides default To and From and keeps the Add Filter selector unclipped', async () => {
		mockUseMediaQuery.mockReturnValue(true);
		const user = userEvent.setup();
		const filterDescriptors = createMailingListMailLogFilterDescriptors({
			mailingLists,
			mailTypes,
			defaultMailTypes: ['broadcast'],
			inboxEnabled: true,
		});

		const setData = vi.fn();
		render(
			<IndexTableControls
				filters={filterDescriptors}
				form={{
					data: { subject: '', to: [], from: '', type: ['broadcast'], read: '' },
					setData,
					submit: vi.fn(),
				}}
				filterLabel="communications"
			/>
		);

		expect(screen.queryByRole('button', { name: /To:.*open filter values/ })).not.toBeInTheDocument();
		expect(screen.queryByRole('button', { name: /From:.*open filter values/ })).not.toBeInTheDocument();
		await user.click(screen.getByRole('button', { name: 'Add filter' }));
		await user.click(screen.getByRole('menuitem', { name: 'To' }));

		const valuesPanel = screen.getByRole('group', { name: 'To values' });
		expect(valuesPanel).toHaveClass('overflow-visible');
		expect(valuesPanel).not.toHaveClass('overflow-hidden');
		const mailingListSelect = within(valuesPanel).getByTestId('mailing-list-select');
		expect(mailingListSelect).toHaveAttribute('data-toolbar-pill', 'false');
		expect(mailingListSelect).toHaveAttribute('data-constrain-width', 'true');
		expect(mailingListSelect).toHaveAttribute('data-truncate-selected-labels', 'true');
		expect(mailingListSelect).toHaveAttribute('data-class-name', 'mt-1 mb-3 w-full min-w-0 max-w-full px-3');
		await user.click(within(mailingListSelect).getByText('Select first mailing list'));
		expect(setData).not.toHaveBeenCalled();
		await user.click(within(valuesPanel).getByText('Apply', { exact: true }));
		expect(setData).toHaveBeenCalledWith('to', ['announcements@example.com']);
	});

	it('renders the desktop From input without duplicate labels or padding', async () => {
		mockUseMediaQuery.mockReturnValue(true);
		const user = userEvent.setup();
		const filterDescriptors = createMailingListMailLogFilterDescriptors({
			mailingLists,
			mailTypes,
			defaultMailTypes: ['broadcast'],
			inboxEnabled: true,
		});

		render(
			<IndexTableControls
				filters={filterDescriptors}
				form={{
					data: { subject: '', to: [], from: 'sender@example.com', type: ['broadcast'], read: '' },
					setData: vi.fn(),
					submit: vi.fn(),
				}}
				filterLabel="communications"
			/>
		);

		await user.click(screen.getByRole('button', { name: /From:.*open filter values/ }));
		const panel = screen.getByRole('heading', { name: 'From' }).closest('.fixed');

		expect(within(panel).getAllByText('From', { exact: true })).toHaveLength(1);
		expect(within(panel).getByRole('textbox', { name: 'From' }).parentElement).not.toHaveClass('px-3', 'pb-2');
	});
});

it('uses the full available width for the mobile drawer mailing-list selector', () => {
	mockUseMediaQuery.mockReturnValue(false);
	render(<MailLogFilterForm inboxEnabled />);

	const mailingListSelect = screen.getByTestId('mailing-list-select');
	const filterField = mailingListSelect.closest('fieldset');

	expect(filterField).toHaveClass('min-w-0');
	expect(mailingListSelect).toHaveAttribute('data-constrain-width', 'true');
	expect(mailingListSelect).toHaveAttribute('data-truncate-selected-labels', 'true');
	expect(mailingListSelect).toHaveAttribute('data-class-name', 'mt-1 mb-3 w-full min-w-0 max-w-full');
});
