import React, { useState } from 'react';
import { fireEvent, render, screen, within } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import MailLogFilters from './MailLogFilters';
import { createCentralMailLogFilterDescriptors } from './MailLogFilterDescriptors';
import { getDefaultFilterData } from '../../../components/filterDescriptorUtils';

vi.mock('react-responsive', () => ({
	useMediaQuery: vi.fn(() => false),
}));

vi.mock('../../../hooks/useRoute', () => ({
	default: () => ({ route: () => '/filters' }),
}));

vi.mock('../../../components/inputs/TenantSelect', async () => {
	const React = await vi.importActual('react');

	return {
		default: ({ options, defaultValue, updateFn }) =>
			React.createElement(
				'button',
				{
					type: 'button',
					'aria-label': 'Select tenant',
					onClick: () => updateFn(options[0]?.value ?? ''),
				},
				defaultValue?.label ?? 'No tenant selected'
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

	return <MailLogFilters form={form} filters={filters} />;
};

it('renders the custom tenant selector through the shared drawer and preserves reset behavior', () => {
	const onSetData = vi.fn();

	render(<CentralMailLogFilterForm onSetData={onSetData} />);
	const tenantField = screen.getByText('Tenant').closest('fieldset');

	expect(screen.getByRole('button', { name: 'Select tenant' })).toHaveTextContent('No tenant selected');
	expect(within(tenantField).getByText('Default')).toBeInTheDocument();

	fireEvent.click(screen.getByRole('button', { name: 'Select tenant' }));

	expect(onSetData).not.toHaveBeenCalled();
	expect(screen.getByRole('button', { name: 'Select tenant' })).toHaveTextContent('Choir');
	expect(within(tenantField).getByText('Modified')).toBeInTheDocument();
	fireEvent.click(screen.getByRole('button', { name: 'Reset Tenant filter' }));

	expect(screen.getByRole('button', { name: 'Select tenant' })).toHaveTextContent('No tenant selected');
	expect(within(tenantField).getByText('Default')).toBeInTheDocument();
	expect(onSetData).not.toHaveBeenCalled();
});