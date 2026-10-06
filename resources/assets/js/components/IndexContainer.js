import React from 'react';
import { useEffect, useState } from 'react';
import { useMediaQuery } from 'react-responsive';
import FilterDialog from './FilterDialog';
import classNames from '../classNames';

const IndexContainer = ({ tableDesktop, tableMobile, emptyState, filterPane, showFilters = false }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const [isMobileFilterOpen, setIsMobileFilterOpen] = useState(showFilters);

	useEffect(() => {
		setIsMobileFilterOpen(showFilters);
	}, [showFilters]);

	return (
		<div className="relative flex min-h-0 flex-1 flex-col divide-y divide-gray-300 lg:flex-row lg:divide-y-0">
			{isDesktop ? (
				<div
					className={classNames(
						'flex-none overflow-hidden transition-[width] duration-300 ease-out',
						showFilters ? 'lg:w-1/5' : 'lg:w-0 xl:w-0'
					)}
				>
					<div
						className={classNames(
							'absolute top-0 left-0 h-full w-1/5 lg:z-10 transition-transform duration-300 ease-out',
							showFilters ? 'translate-x-0' : '-translate-x-full'
						)}
					>
						{filterPane}
					</div>
				</div>
			) : (
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
					<div className="min-h-0 flex-1 overflow-hidden bg-white shadow block">{tableMobile}</div>
				)}
			</div>
		</div>
	);
};

export default IndexContainer;
