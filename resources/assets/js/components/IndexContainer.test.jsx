import React, { useState } from 'react';
import { fireEvent, render, screen, waitFor } from '@testing-library/react';
import '@testing-library/jest-dom';
import { vi } from 'vitest';
import IndexContainer from './IndexContainer';
import FilterDrawer from './Filters/FilterDrawer';
import Filters from './Filters/Filters';

const { mockUseMediaQuery } = vi.hoisted(() => ({ mockUseMediaQuery: vi.fn() }));

vi.mock('react-responsive', () => ({
	useMediaQuery: (...args) => mockUseMediaQuery(...args),
}));

const lifecycleFilters = [
	{
		name: 'status',
		label: 'Status',
		type: 'choice',
		defaultValue: 'active',
		options: [
			{ id: 'active', name: 'Active' },
			{ id: 'inactive', name: 'Inactive' },
		],
	},
];

const FilterLifecycleHarness = () => {
	const [showFilters, setShowFilters] = useState(true);
	const [data, setData] = useState({ status: 'active' });

	return (
		<>
			<button onClick={() => setShowFilters(false)}>Close filters externally</button>
			<button onClick={() => setShowFilters(true)}>Open filters</button>
			<div data-testid="applied-status">{data.status}</div>
			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<Filters
							routeName="filters.index"
							filters={lifecycleFilters}
							form={{
								data,
								defaultFilterData: { status: 'active' },
								setData: (nameOrData, value) =>
									setData(previousData =>
										typeof nameOrData === 'object'
											? nameOrData
											: { ...previousData, [nameOrData]: value }
									),
								submit: () => {},
							}}
						/>
					</FilterDrawer>
				}
				tableMobile={<div>Mobile table</div>}
			/>
		</>
	);
};

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

	it.each(['close button', 'backdrop', 'Escape'])(
		'discards staged filter edits after dismissal via %s',
		async dismissal => {
			render(<FilterLifecycleHarness />);
			fireEvent.click(screen.getByRole('radio', { name: 'Inactive' }));

			expect(screen.getByRole('radio', { name: 'Inactive' })).toBeChecked();
			expect(screen.getByTestId('applied-status')).toHaveTextContent('active');

			if (dismissal === 'close button') {
				fireEvent.click(screen.getByRole('button', { name: 'Close filter drawer' }));
			} else if (dismissal === 'backdrop') {
				const dialog = screen.getByRole('dialog');
				fireEvent.click(dialog.querySelector('[aria-hidden="true"]'));
			} else {
				fireEvent.keyDown(document, { key: 'Escape' });
			}

			await waitFor(() => expect(screen.queryByRole('dialog')).not.toBeInTheDocument());

			fireEvent.click(screen.getByRole('button', { name: 'Close filters externally' }));
			fireEvent.click(screen.getByRole('button', { name: 'Open filters' }));
			await screen.findByRole('dialog');

			expect(screen.getByRole('radio', { name: 'Active' })).toBeChecked();
			expect(screen.getByRole('radio', { name: 'Inactive' })).not.toBeChecked();
		}
	);

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
