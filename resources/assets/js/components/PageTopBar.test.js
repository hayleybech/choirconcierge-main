import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { PageTopNavigation, default as PageTopBar } from './PageTopBar';
import { SidebarProvider } from '../contexts/sidebar-context';

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
});
