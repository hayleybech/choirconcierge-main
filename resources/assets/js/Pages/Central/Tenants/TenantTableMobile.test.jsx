import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import TenantTableMobile from './TenantTableMobile';

vi.mock('../../../components/Pagination', () => ({ default: () => null }));

test('does not duplicate filter controls inside the mobile table', () => {
	render(
		<TenantTableMobile
			tenants={[]}
			pagination={{ total: 0, links: [] }}
		/>
	);

	expect(screen.queryByRole('button', { name: 'Filter/Sort' })).toBeNull();
	expect(screen.getByRole('list')).toBeTruthy();
});