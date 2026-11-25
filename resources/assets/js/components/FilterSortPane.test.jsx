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

it('keeps an Apply action visible at the bottom of a scrollable filter pane', () => {
	const applyFilters = vi.fn();
	const closeDrawer = vi.fn();
	const { container } = render(
		<FilterDrawer closeFn={closeDrawer} onApply={applyFilters}>
			<div>Filter fields</div>
		</FilterDrawer>
	);

	const applyButton = screen.getByRole('button', { name: 'Apply' });

	expect(applyButton.closest('.sticky')).toHaveClass('sticky', 'bottom-0');
	expect(applyButton).toHaveClass('flex-1');
	expect(applyButton.querySelector('.fa-check')).toBeInTheDocument();
	expect(container.querySelector('.overflow-y-auto')).toContainElement(screen.getByText('Filter fields'));
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

	expect(clearAllButton).toHaveClass('flex-1');
	expect(clearAllButton.querySelector('.fa-undo')).toBeInTheDocument();
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
