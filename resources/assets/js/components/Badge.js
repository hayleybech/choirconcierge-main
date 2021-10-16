import React from 'react';

const Badge = ({ children }) => (
    <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">
        {children}
    </span>
);

export default Badge;