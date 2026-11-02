import React from 'react';
import { useEffect, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import FilterDialog from './FilterDialog';
 
const IndexContainer = ({
	tableDesktop,
	tableMobile,
	emptyState,
	filterPane,
	showFilters = false,
	tableControls = null,
	tableSummary = null,
}) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const [isMobileFilterOpen, setIsMobileFilterOpen] = useState(showFilters);

	useEffect(() => {
		setIsMobileFilterOpen(showFilters);
	}, [showFilters]);

	return (
		<div className="relative flex min-h-0 flex-none flex-col lg:flex-1">
			{tableControls && (
				<div className="sticky top-11.75 z-30 shrink-0 bg-white lg:static lg:z-auto">{tableControls}</div>
			)}
			{tableSummary}
			<div className="relative flex min-h-0 flex-none flex-col divide-y divide-gray-300 lg:flex-1 lg:flex-row lg:divide-y-0">
				{!isDesktop && filterPane && (
					<FilterDialog isOpen={isMobileFilterOpen} setIsOpen={setIsMobileFilterOpen}>
						{filterPane}
					</FilterDialog>
				)}
				<div className="flex min-h-0 min-w-0 flex-1 flex-col lg:overflow-x-auto lg:border-l lg:border-gray-300">
					{emptyState ? (
						emptyState
					) : isDesktop ? (
						<div className="min-h-0 flex-1 overflow-hidden">{tableDesktop}</div>
					) : (
						<div className="flex-none bg-white shadow block">{tableMobile}</div>
					)}
				</div>
			</div>
		</div>
	);
};

export default IndexContainer;
