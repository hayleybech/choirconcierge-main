import React from 'react';

const SectionSubtitle = ({ className, children }) => (
    <h3 className={`text-lg leading-5 font-semibold text-gray-900 mb-3 ${className}`}>{children}</h3>
);

export default SectionSubtitle;