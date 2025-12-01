import React from 'react';
import {useMediaQuery} from "react-responsive";

const IndexContainer = ({ tableDesktop, tableMobile, emptyState, filterPane, showFilters }) => {
    const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

    return (
        <div className="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-300">
            {showFilters && (
                <div className="lg:w-1/5 xl:w-1/6 lg:z-10">
                    {filterPane}
                </div>
            )}
            <div className="grow lg:overflow-x-auto">
                {emptyState
                    ? emptyState
                    : (<>
                        {isDesktop ? (
                            <div className="flex-col overflow-y-hidden">
                                {tableDesktop}
                            </div>
                        ) : (
                            <div className="bg-white shadow block">
                                {tableMobile}
                            </div>

                        )}
                    </>)
                }
            </div>
        </div>
    );
}

export default IndexContainer;