import React, { useContext } from 'react';

export const SidebarContext = React.createContext(null);

export const SidebarProvider = ({ setSidebarOpen, children }) => (
	<SidebarContext.Provider value={{ setSidebarOpen }}>{children}</SidebarContext.Provider>
);

export const useSidebar = () => {
	const context = useContext(SidebarContext);

	if (!context) {
		throw new Error('useSidebar must be used within a SidebarProvider');
	}

	return context;
};
