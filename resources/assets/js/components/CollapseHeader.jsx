import React from "react";

const CollapseHeader = ({ children }) => (
    <div className="py-4 px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between space-x-4 bg-gray-50 border-b border-gray-200">
        {children}
    </div>
);

export default CollapseHeader;