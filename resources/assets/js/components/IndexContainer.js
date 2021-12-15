import React from 'react';

const IndexContainer = ({ TableDesktop, TableMobile, Filters, showFilters }) => (
    <div className="flex flex-col lg:flex-row">
        {showFilters && (
            <div className="lg:w-1/5 xl:w-1/6 border-b lg:border-r border-gray-300 lg:z-10">
                <Filters />
            </div>
        )}
        <div className="flex-grow lg:overflow-x-auto">
            <div className="hidden lg:flex flex-col overflow-y-hidden">
                <TableDesktop />
            </div>

            <div className="bg-white shadow block lg:hidden">
                <TableMobile />
            </div>
        </div>
    </div>
);

export default IndexContainer;