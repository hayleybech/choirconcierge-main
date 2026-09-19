import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { PageTopBarTitle, PageTopNavigation, default as PageTopBar } from './PageTopBar';
import { SidebarProvider } from '../contexts/sidebar-context';

jest.mock('react-responsive', () => ({
	useMediaQuery: jest.fn(),
}));

import { useMediaQuery } from 'react-responsive';

const renderWithSidebar = (ui, setSidebarOpen = jest.fn()) =>
	render(<SidebarProvider setSidebarOpen={setSidebarOpen}>{ui}</SidebarProvider>);

describe('PageTopBar', () => {
	it('opens the sidebar through context', () => {
		const setSidebarOpen = jest.fn();

		renderWithSidebar(<PageTopBar>Page title</PageTopBar>, setSidebarOpen);
		fireEvent.click(screen.getByRole('button', { name: 'Open sidebar' }));

		expect(setSidebarOpen).toHaveBeenCalledWith(true);
	});
});

describe('PageTopNavigation', () => {
	beforeEach(() => {
		useMediaQuery.mockReturnValue(false);
	});

	it('uses the parent breadcrumbs for desktop navigation', () => {
		render(
			<PageTopNavigation
				breadcrumbs={[
					{ name: 'Singers', url: '/singers' },
					{ name: 'Jane Doe', url: '/singers/1' },
				]}
			/>
		);

		expect(screen.getByRole('link', { name: 'Singers' }).getAttribute('href')).toBe('/singers');
	});

	it('uses the direct parent for mobile back navigation', () => {
		useMediaQuery.mockReturnValue(true);

		render(
			<PageTopNavigation
				breadcrumbs={[
					{ name: 'Choirs', url: '/choirs' },
					{ name: 'Singers', url: '/choirs/1/singers' },
					{ name: 'Jane Doe', url: '/choirs/1/singers/1' },
				]}
			/>
		);

		expect(screen.getByRole('link', { name: 'Back to parent page' }).getAttribute('href')).toBe(
			'/choirs/1/singers'
		);
	});

	it('uses the last breadcrumb as the title and omits it from navigation', () => {
		render(
			<PageTopNavigation
				breadcrumbs={[
					{ name: 'Singers', url: '/singers' },
					{ name: 'Jane Doe', url: '/singers/1' },
				]}
			/>
		);

		expect(screen.getByRole('heading', { name: 'Jane Doe' })).toBeTruthy();
		expect(screen.queryByRole('link', { name: 'Jane Doe' })).toBeNull();
		expect(screen.getByRole('link', { name: 'Singers' })).toBeTruthy();
	});

	it('allows long titles to shrink within the top bar', () => {
		render(<PageTopBarTitle>A very long page title</PageTopBarTitle>);

		const title = screen.getByRole('heading', { name: 'A very long page title' });

		expect(title.classList.contains('min-w-0')).toBe(true);
		expect(title.classList.contains('flex-1')).toBe(true);
		expect(title.classList.contains('truncate')).toBe(true);
	});
});
