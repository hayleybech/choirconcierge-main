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
		expect(screen.getByText('Filter controls')).toBeTruthy();
		expect(screen.getByText('Desktop table')).toBeTruthy();
	});
});
