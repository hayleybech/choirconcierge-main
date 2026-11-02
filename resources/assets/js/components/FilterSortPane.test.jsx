import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import FilterSortPane from './FilterSortPane';

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
