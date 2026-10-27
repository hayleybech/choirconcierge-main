import React from 'react';
import { fireEvent, render, screen } from '@testing-library/react';
import { vi } from 'vitest';
import { SidebarProvider, useSidebar } from './sidebar-context';

const SidebarAction = ({ value }) => {
	const { setSidebarOpen } = useSidebar();

	return <button onClick={() => setSidebarOpen(value)}>Update sidebar</button>;
};

describe('SidebarProvider', () => {
	afterEach(() => {
		delete window.ReactNativeWebView;
	});

	it('opens the local sidebar outside a React Native WebView', () => {
		const setSidebarOpen = vi.fn();

		render(
			<SidebarProvider setSidebarOpen={setSidebarOpen}>
				<SidebarAction value={true} />
			</SidebarProvider>
		);

		fireEvent.click(screen.getByRole('button', { name: 'Update sidebar' }));

		expect(setSidebarOpen).toHaveBeenCalledWith(true);
	});

	it('asks the React Native host to open the sidebar in a WebView', () => {
		const setSidebarOpen = vi.fn();
		const postMessage = vi.fn();
		window.ReactNativeWebView = { postMessage };

		render(
			<SidebarProvider setSidebarOpen={setSidebarOpen}>
				<SidebarAction value={true} />
			</SidebarProvider>
		);

		fireEvent.click(screen.getByRole('button', { name: 'Update sidebar' }));

		expect(setSidebarOpen).not.toHaveBeenCalled();
		expect(postMessage).toHaveBeenCalledWith(JSON.stringify({ action: 'openSidebar' }));
	});

	it('still closes the local sidebar in a WebView', () => {
		const setSidebarOpen = vi.fn();
		window.ReactNativeWebView = { postMessage: vi.fn() };

		render(
			<SidebarProvider setSidebarOpen={setSidebarOpen}>
				<SidebarAction value={false} />
			</SidebarProvider>
		);

		fireEvent.click(screen.getByRole('button', { name: 'Update sidebar' }));

		expect(setSidebarOpen).toHaveBeenCalledWith(false);
	});
});
