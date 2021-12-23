import React from 'react';

const SectionHeader = ({children}) => (
    <div className="mb-2 flex flex-wrap items-center justify-between space-x-4">
        {children}
    </div>
);

export default SectionHeader;