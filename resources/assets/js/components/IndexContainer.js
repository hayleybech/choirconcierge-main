import React from 'react';
import { useEffect, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import Dialog from './Dialog';

const IndexContainer = ({ tableDesktop, tableMobile, emptyState, filterPane, showFilters }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const [isMobileFilterOpen, setIsMobileFilterOpen] = useState(showFilters);

	useEffect(() => {
		setIsMobileFilterOpen(showFilters);
	}, [showFilters]);

	return (
		<div className="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-300">
			{isDesktop ? (
				showFilters && <div className="lg:w-1/5 xl:w-1/6 lg:z-10">{filterPane}</div>
			) : (
				<Dialog isOpen={isMobileFilterOpen} setIsOpen={setIsMobileFilterOpen} icon="">
					{filterPane}
				</Dialog>
			)}
			<div className="grow lg:overflow-x-auto">
				{emptyState ? (
					emptyState
				) : isDesktop ? (
					<div className="flex-col overflow-y-hidden">{tableDesktop}</div>
				) : (
					<div className="bg-white shadow block">{tableMobile}</div>
				)}
			</div>
		</div>
	);
};

export default IndexContainer;
