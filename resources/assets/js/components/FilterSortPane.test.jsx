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
	const { container } = render(
		<FilterDrawer closeFn={vi.fn()} onApply={applyFilters}>
			<div>Filter fields</div>
		</FilterDrawer>
	);

	const applyButton = screen.getByRole('button', { name: 'Apply' });

	expect(applyButton.closest('.sticky')).toHaveClass('sticky', 'bottom-0');
	expect(screen.getByRole('button', { name: 'Cancel' })).toHaveClass('flex-1');
	expect(applyButton).toHaveClass('flex-1');
	expect(container.querySelector('.overflow-y-auto')).toContainElement(screen.getByText('Filter fields'));
	fireEvent.click(applyButton);

	expect(applyFilters).toHaveBeenCalledOnce();
});

it('calls the cancel action without applying the staged filters', () => {
	const applyFilters = vi.fn();
	const cancelFilters = vi.fn();
	render(
		<FilterDrawer closeFn={vi.fn()} onCancel={cancelFilters} onApply={applyFilters}>
			<div>Filter fields</div>
		</FilterDrawer>
	);

	fireEvent.click(screen.getByRole('button', { name: 'Cancel' }));

	expect(cancelFilters).toHaveBeenCalledOnce();
	expect(applyFilters).not.toHaveBeenCalled();
});
