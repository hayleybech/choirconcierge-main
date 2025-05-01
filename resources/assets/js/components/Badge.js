import React from 'react';

const Badge = ({ colour = 'bg-gray-200', children }) => (
    <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-800 ${colour}`}>
        {children}
    </span>
);

export default Badge;