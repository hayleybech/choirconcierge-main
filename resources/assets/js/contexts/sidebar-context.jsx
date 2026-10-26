import React, { useContext } from 'react';
import { sendMessageToRN } from '../lib/reactNative';

export const SidebarContext = React.createContext(null);

export const SidebarProvider = ({ setSidebarOpen, children }) => {
	const handleSidebarOpen = open => {
		if (open === true && window.ReactNativeWebView) {
			sendMessageToRN({ action: 'openSidebar' });

			return;
		}

		setSidebarOpen(open);
	};

	return (
		<SidebarContext.Provider value={{ setSidebarOpen: handleSidebarOpen }}>
			{children}
		</SidebarContext.Provider>
	);
};

export const useSidebar = () => {
	const context = useContext(SidebarContext);

	if (!context) {
		throw new Error('useSidebar must be used within a SidebarProvider');
	}

	return context;
};
