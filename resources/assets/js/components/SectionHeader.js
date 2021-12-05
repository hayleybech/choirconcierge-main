import React from 'react';

const SectionHeader = ({children}) => (
    <div className="mb-5 sm:flex sm:items-center sm:justify-between space-y-3 sm:space-y-0 sm:space-x-4">
        {children}
    </div>
);

export default SectionHeader;