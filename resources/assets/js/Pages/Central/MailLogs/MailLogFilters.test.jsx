import React, { useState } from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import Filters from '../../../components/Filters';
import IndexTableControls from '../../../components/IndexTableControls';
import { createCentralMailLogFilterDescriptors } from './MailLogFilterDescriptors';
import { getDefaultFilterData } from '../../../components/filterDescriptorUtils';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn(() => false) }));

vi.mock('react-responsive', () => ({
	useMediaQuery: mockUseMediaQuery,
}));

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: () => '/filters' }),
}));

vi.mock('../../../components/inputs/TenantSelect', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({ options, defaultValue, value, updateFn, className }) =>
			React.createElement(
				'button',
				{
					'type': 'button',
					'aria-label': 'Select tenant',
					'data-testid': 'tenant-selector',
					'data-class-name': className,
					'onClick': () => updateFn(options[0]?.value ?? ''),
				},
				value?.label ?? defaultValue?.label ?? 'No tenant selected'
			),
	};
});

const tenants = [{ id: 'tenant-1', name: 'Choir' }];
const mailTypes = [{ id: 'broadcast', name: 'Broadcast' }];

const CentralMailLogFilterForm = ({ onSetData }) => {
	const filters = createCentralMailLogFilterDescriptors({
		tenants,
		mailTypes,
		defaultMailTypes: ['broadcast'],
	});
	const [data, updateData] = useState({ subject: '', to: '', from: '', tenant: '', type: ['broadcast'] });
	const form = {
		data,
		defaultFilterData: getDefaultFilterData(filters),
		searchableFilterNames: [],
		setData: (name, value) => {
			onSetData(name, value);
			updateData(previousData => ({ ...previousData, [name]: value }));
		},
		submit: vi.fn(),
	};

	return <Filters routeName="central.mail-logs.index" form={form} filters={filters} />;
};

const CentralMailLogControls = ({ initialData, onSetData }) => {
	const filters = createCentralMailLogFilterDescriptors({
		tenants,
		mailTypes,
		defaultMailTypes: ['broadcast'],
	});
	const [data, updateData] = useState(initialData);

	return (
		<IndexTableControls
			filters={filters}
			form={{
				data,
				setData: (name, value) => {
					onSetData(name, value);
					updateData(previousData => ({ ...previousData, [name]: value }));
				},
				submit: vi.fn(),
			}}
			filterLabel="mail logs"
		/>
	);
};

it('renders the custom tenant selector through the shared drawer and preserves reset behavior', () => {
	const onSetData = vi.fn();

	render(<CentralMailLogFilterForm onSetData={onSetData} />);
	const tenantField = screen.getByText('Tenant').closest('fieldset');

	expect(screen.getByRole('button', { name: 'Select tenant' })).toHaveTextContent('No tenant selected');
	expect(within(tenantField).queryByText('Default')).not.toBeInTheDocument();

	fireEvent.click(screen.getByRole('button', { name: 'Select tenant' }));

	expect(onSetData).not.toHaveBeenCalled();
	expect(screen.getByRole('button', { name: 'Select tenant' })).toHaveTextContent('Choir');
	expect(within(tenantField).getByTitle('Filter differs from default')).toBeInTheDocument();
	fireEvent.click(screen.getByRole('button', { name: 'Reset Tenant filter' }));

	expect(screen.getByRole('button', { name: 'Select tenant' })).toHaveTextContent('No tenant selected');
	expect(within(tenantField).queryByTitle('Filter differs from default')).not.toBeInTheDocument();
	expect(onSetData).not.toHaveBeenCalled();
});

it('puts tenant selection in both the Add Filter panel and the desktop filter pill', async () => {
	mockUseMediaQuery.mockReturnValue(true);
	const user = userEvent.setup();
	const onSetData = vi.fn();
	render(
		<CentralMailLogControls
			initialData={{ subject: '', to: '', from: '', tenant: '', type: ['broadcast'] }}
			onSetData={onSetData}
		/>
	);

	await user.click(screen.getByRole('button', { name: 'Add filter' }));
	await user.click(screen.getByRole('menuitem', { name: 'Tenant' }));
	expect(screen.getByRole('group', { name: 'Tenant values' })).toHaveClass('overflow-visible');
	expect(screen.getByTestId('tenant-selector')).toHaveAttribute(
		'data-class-name',
		'mt-1 mb-3 w-full min-w-0 max-w-full px-3'
	);
	await user.click(screen.getByTestId('tenant-selector'));
	await user.click(screen.getByRole('menuitem', { name: 'Apply' }));

	expect(onSetData).toHaveBeenCalledWith('tenant', 'tenant-1');
	await user.click(screen.getByRole('button', { name: /Tenant:.*open filter values/ }));
	expect(screen.getByTestId('tenant-selector')).toHaveTextContent('Choir');
});

it('uses desktop pills for To and From and omits them from the mobile search rail', async () => {
	const initialData = {
		subject: '',
		to: 'recipient@example.com',
		from: 'sender@example.com',
		tenant: '',
		type: ['broadcast'],
	};
	mockUseMediaQuery.mockReturnValue(true);
	const { unmount } = render(<CentralMailLogControls initialData={initialData} onSetData={vi.fn()} />);

	const desktop = screen.getByTestId('desktop-index-table-controls');
	expect(within(desktop).queryByRole('searchbox', { name: 'Search mail logs by to' })).not.toBeInTheDocument();
	expect(within(desktop).queryByRole('searchbox', { name: 'Search mail logs by from' })).not.toBeInTheDocument();

	const user = userEvent.setup();
	await user.click(screen.getByRole('button', { name: /To:.*open filter values/ }));
	const toPanel = screen.getByRole('heading', { name: 'To' }).closest('.fixed');
	expect(within(toPanel).getAllByText('To', { exact: true })).toHaveLength(1);
	expect(within(toPanel).getByRole('textbox', { name: 'To' }).parentElement).not.toHaveClass('px-3', 'pb-2');

	await user.click(screen.getByRole('button', { name: /From:.*open filter values/ }));
	const fromPanel = screen.getByRole('heading', { name: 'From' }).closest('.fixed');
	expect(within(fromPanel).getAllByText('From', { exact: true })).toHaveLength(1);
	expect(within(fromPanel).getByRole('textbox', { name: 'From' })).toHaveValue('sender@example.com');
	expect(within(fromPanel).getByRole('textbox', { name: 'From' }).parentElement).not.toHaveClass('px-3', 'pb-2');
	unmount();

	mockUseMediaQuery.mockReturnValue(false);
	render(<CentralMailLogControls initialData={initialData} onSetData={vi.fn()} />);
	const mobile = screen.getByTestId('mobile-index-table-controls');
	expect(within(mobile).queryByRole('searchbox', { name: 'Search mail logs by to' })).not.toBeInTheDocument();
	expect(within(mobile).queryByRole('searchbox', { name: 'Search mail logs by from' })).not.toBeInTheDocument();
});

it('does not show default To and From values in the desktop rail', () => {
	mockUseMediaQuery.mockReturnValue(true);
	render(
		<CentralMailLogControls
			initialData={{ subject: '', to: '', from: '', tenant: '', type: ['broadcast'] }}
			onSetData={vi.fn()}
		/>
	);

	expect(screen.queryByRole('button', { name: /To:.*open filter values/ })).not.toBeInTheDocument();
	expect(screen.queryByRole('button', { name: /From:.*open filter values/ })).not.toBeInTheDocument();
});
