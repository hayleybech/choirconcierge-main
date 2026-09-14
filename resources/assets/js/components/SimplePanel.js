import React from 'react';

const SimplePanel = ({ children }) => <div className="bg-gray-50 py-4 px-4 sm:px-6 lg:px-8">{ children }</div>;

export default SimplePanel;
export const SimplePanelWithoutPadding = ({ children }) => <div className="bg-gray-50">{children}</div>;