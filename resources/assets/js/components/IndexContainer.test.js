import React from 'react';
import { render, screen } from '@testing-library/react';
import IndexContainer from './IndexContainer';

const mockUseMediaQuery = jest.fn();

jest.mock('react-responsive', () => ({
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
		expect(screen.getByRole('dialog').className).toContain('fixed inset-x-0 top-[47px] bottom-0');
		expect(screen.getByText('Mobile table').parentElement.parentElement.className).toContain('flex-1');
		expect(screen.getByText('Mobile table').parentElement.className).toContain('overflow-hidden');
		expect(screen.queryByText('Cancel')).toBeNull();
	});

	it('keeps filters in the sidebar on desktop', () => {
		mockUseMediaQuery.mockReturnValue(true);

		render(
			<IndexContainer
				showFilters
				filterPane={<div>Filter controls</div>}
				tableDesktop={<div>Desktop table</div>}
			/>
			);

		expect(screen.queryByRole('dialog')).toBeNull();
		const filterPane = screen.getByText('Filter controls').parentElement.parentElement;

		expect(filterPane).toBeTruthy();
		expect(filterPane.className).toContain('lg:w-1/5');
		expect(filterPane.className).toContain('transition-[width]');
		expect(filterPane.firstElementChild.className).toContain('w-1/5');
		expect(screen.getByText('Desktop table').parentElement.parentElement.className).toContain('lg:border-l');
		expect(screen.getByText('Desktop table').parentElement.parentElement.parentElement.className).toContain('flex-1');
		expect(screen.getByText('Desktop table').parentElement.className).toContain('overflow-hidden');
		expect(screen.getByText('Desktop table')).toBeTruthy();
	});

	it('keeps the desktop filter pane mounted while closed so its width can animate', () => {
		mockUseMediaQuery.mockReturnValue(true);

		render(
			<IndexContainer
				filterPane={<div>Filter controls</div>}
				tableDesktop={<div>Desktop table</div>}
			/>
		);

		const filterPane = screen.getByText('Filter controls').parentElement.parentElement;

		expect(filterPane.className).toContain('lg:w-0');
		expect(filterPane.className).toContain('overflow-hidden');
		expect(filterPane.firstElementChild.className).toContain('w-1/5');
		expect(screen.getByText('Desktop table')).toBeTruthy();
	});

	it('does not open a filter dialog when filters are not configured', () => {
		render(<IndexContainer tableMobile={<div>Mobile table</div>} />);

		expect(screen.queryByRole('dialog')).toBeNull();
		expect(screen.getByText('Mobile table')).toBeTruthy();
	});
});
