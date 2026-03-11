import React from 'react';

const Badge = ({ colour = 'bg-gray-200', display = 'inline-flex', children }) => {
	const textColourDefault = colour.includes('text-') ? '' : 'text-gray-800';

	return (
		<span className={`${display} items-center px-2 py-0.5 rounded text-xs font-medium ${textColourDefault} ${colour}`}>
        {children}
    </span>
	);
}

export default Badge;