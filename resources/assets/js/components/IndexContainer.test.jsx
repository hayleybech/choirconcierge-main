import React from 'react';
import { render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import IndexContainer from './IndexContainer';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn() }));

vi.mock('react-responsive', () => ({
	useMediaQuery: (...args) => mockUseMediaQuery(...args),
}));

describe('IndexContainer', () => {
	beforeEach(() => {
		mockUseMediaQuery.mockReturnValue(false);
	});

	it('opens filters in a modal on mobile', () => {
		render(
			<IndexContainer
				showFilters
				filterPane={<div>Filter controls</div>}
				tableMobile={<div>Mobile table</div>}
			/>
		);

		expect(screen.getByRole('dialog')).toBeTruthy();
		expect(screen.getByText('Filter controls')).toBeTruthy();
		expect(screen.getByText('Mobile table')).toBeTruthy();
		expect(screen.queryByText('Cancel')).toBeNull();
	});

	it('lets mobile table content grow with the page so it can scroll', () => {
		render(<IndexContainer tableMobile={<div>Mobile table</div>} />);

		const mobileTableContainer = screen.getByText('Mobile table').parentElement;

		expect(mobileTableContainer.className).not.toContain('overflow-hidden');
		expect(mobileTableContainer.className).toContain('flex-none');
	});

	it('does not render the filter pane on desktop', () => {
		mockUseMediaQuery.mockReturnValue(true);

		render(
			<IndexContainer
				showFilters
				filterPane={<div>Filter controls</div>}
				tableDesktop={<div>Desktop table</div>}
			/>
			);

		expect(screen.queryByRole('dialog')).toBeNull();
		expect(screen.queryByText('Filter controls')).toBeNull();
		expect(screen.getByText('Desktop table')).toBeTruthy();
	});

	it('keeps the mobile table controls sticky below the mobile top bar', () => {
		mockUseMediaQuery.mockReturnValue(false);

		render(<IndexContainer tableControls={<div data-testid="table-controls">Table controls</div>} />);

		expect(screen.getByTestId('table-controls').parentElement.className).toContain('sticky');
		expect(screen.getByTestId('table-controls').parentElement.className).toContain('top-11.75');
	});

	it('keeps table controls outside the filter and table layout row', () => {
		mockUseMediaQuery.mockReturnValue(true);

		const { container } = render(
			<IndexContainer
				tableControls={<div data-testid="table-controls">Table controls</div>}
				filterPane={<div>Filter controls</div>}
				tableDesktop={<div>Desktop table</div>}
			/>
		);

		const controlsWrapper = screen.getByTestId('table-controls').parentElement;

		expect(controlsWrapper.parentElement).toBe(container.firstElementChild);
		expect(controlsWrapper.nextElementSibling).toBeTruthy();
	});

	it('renders a table summary immediately after the controls row', () => {
		const { container } = render(
			<IndexContainer
				tableControls={<div data-testid="table-controls">Controls</div>}
				tableSummary={<div data-testid="table-summary">Summary</div>}
				tableMobile={<div>Mobile table</div>}
			/>
		);
		const controlsWrapper = screen.getByTestId('table-controls').parentElement;
		const summary = screen.getByTestId('table-summary');

		expect(controlsWrapper.parentElement).toBe(container.firstElementChild);
		expect(controlsWrapper.nextElementSibling).toBe(summary);
		expect(summary.nextElementSibling).toBeTruthy();
	});

	it('does not open a filter dialog when filters are not configured', () => {
		render(<IndexContainer tableMobile={<div>Mobile table</div>} />);

		expect(screen.queryByRole('dialog')).toBeNull();
		expect(screen.getByText('Mobile table')).toBeTruthy();
	});
});
