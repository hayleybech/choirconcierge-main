import React from 'react';

const IndexContainer = ({ tableDesktop, tableMobile, filterPane, showFilters }) => (
    <div className="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-300">
        {showFilters && (
            <div className="lg:w-1/5 xl:w-1/6 lg:z-10">
                {filterPane}
            </div>
        )}
        <div className="grow lg:overflow-x-auto">
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