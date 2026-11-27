import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterDrawer from './FilterDrawer';

vi.mock('react-responsive', () => ({
	useMediaQuery: vi.fn(),
}));

import { useMediaQuery } from 'react-responsive';

beforeEach(() => {
	useMediaQuery.mockReturnValue(false);
});

it('applies filters and closes the drawer', () => {
	const applyFilters = vi.fn();
	const closeDrawer = vi.fn();
	render(
		<FilterDrawer closeFn={closeDrawer} onApply={applyFilters}>
			<div>Filter fields</div>
		</FilterDrawer>
	);

	const applyButton = screen.getByRole('button', { name: 'Apply' });

	fireEvent.click(applyButton);

	expect(applyFilters).toHaveBeenCalledOnce();
	expect(closeDrawer).toHaveBeenCalledOnce();
});

	it('applies reset filters and closes the drawer', () => {
	const applyFilters = vi.fn();
	const clearAllFilters = vi.fn(applyFilters);
	const closeDrawer = vi.fn();
	render(
		<FilterDrawer closeFn={closeDrawer} onApply={applyFilters} onClearAll={clearAllFilters}>
			<div>Filter fields</div>
		</FilterDrawer>
	);

	const clearAllButton = screen.getByRole('button', { name: 'Reset to Default' });

	fireEvent.click(clearAllButton);

	expect(clearAllFilters).toHaveBeenCalledOnce();
	expect(applyFilters).toHaveBeenCalledOnce();
	expect(closeDrawer).toHaveBeenCalledOnce();
});

it('discards staged filters when the drawer is closed without applying', () => {
	const applyFilters = vi.fn();
	const cancelFilters = vi.fn();
	render(
		<FilterDrawer closeFn={vi.fn()} onCancel={cancelFilters} onApply={applyFilters}>
			<div>Filter fields</div>
		</FilterDrawer>
	);

	fireEvent.click(screen.getByRole('button', { name: 'Close filter drawer' }));

	expect(cancelFilters).toHaveBeenCalledOnce();
	expect(applyFilters).not.toHaveBeenCalled();
});
