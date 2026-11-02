import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterSortPane from './FilterSortPane';

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
		<FilterSortPane
			sorts={null}
			filters={<div>Filter fields</div>}
			closeFn={vi.fn()}
			onApply={applyFilters}
		/>
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
		<FilterSortPane
			sorts={null}
			filters={<div>Filter fields</div>}
			closeFn={vi.fn()}
			onCancel={cancelFilters}
			onApply={applyFilters}
		/>
	);

	fireEvent.click(screen.getByRole('button', { name: 'Cancel' }));

	expect(cancelFilters).toHaveBeenCalledOnce();
	expect(applyFilters).not.toHaveBeenCalled();
});

it('keeps sort controls out of the mobile filter pane', () => {
	render(<FilterSortPane sorts={<div>Sort controls</div>} filters={<div>Filter fields</div>} closeFn={vi.fn()} />);

	expect(screen.queryByText('Sort controls')).not.toBeInTheDocument();
	expect(screen.getByText('Filter fields')).toBeInTheDocument();
});

it('only shows sort controls on desktop when explicitly requested', () => {
	useMediaQuery.mockReturnValue(true);
	render(
		<FilterSortPane
			sorts={<div>Sort controls</div>}
			filters={<div>Filter fields</div>}
			closeFn={vi.fn()}
			showSortsOnDesktop
		/>
	);

	expect(screen.getByText('Sort controls')).toBeInTheDocument();
});
