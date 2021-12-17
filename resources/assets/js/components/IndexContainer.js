import React from 'react';

const IndexContainer = ({ tableDesktop, tableMobile, filters, showFilters }) => (
    <div className="flex flex-col lg:flex-row">
        {showFilters && (
            <div className="lg:w-1/5 xl:w-1/6 border-b lg:border-r border-gray-300 lg:z-10">
                {filters}
            </div>
        )}
        <div className="flex-grow lg:overflow-x-auto">
            <div className="hidden lg:flex flex-col overflow-y-hidden">
                {tableDesktop}
            </div>

            <div className="bg-white shadow block lg:hidden">
                {tableMobile}
            </div>
        </div>
    </div>
);

export default IndexContainer;