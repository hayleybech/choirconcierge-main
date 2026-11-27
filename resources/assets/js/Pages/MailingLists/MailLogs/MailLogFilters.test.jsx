import React, { useState } from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import Filters from '../../../components/Filters';
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
		}) =>
			React.createElement(
				'div',
				{
					'data-testid': 'mailing-list-select',
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

	return <Filters routeName="communications.index" form={form} filters={filters} />;
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
		await user.click(screen.getByRole('button', { name: 'Clear selected mailing lists' }));

		expect(setData).toHaveBeenCalledWith('to', []);
	});

	it('hides default To and From and applies selections from the Add Filter panel', async () => {
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
		const mailingListSelect = within(valuesPanel).getByTestId('mailing-list-select');
		await user.click(within(mailingListSelect).getByText('Select first mailing list'));
		expect(setData).not.toHaveBeenCalled();
		await user.click(within(valuesPanel).getByText('Apply', { exact: true }));
		expect(setData).toHaveBeenCalledWith('to', ['announcements@example.com']);
	});

	it('renders the desktop From input with an accessible label', async () => {
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
		expect(screen.getByRole('heading', { name: 'From' })).toBeInTheDocument();
		expect(screen.getByRole('textbox', { name: 'From' })).toHaveValue('sender@example.com');
	});
});
