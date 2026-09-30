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
		<div className="relative flex flex-col lg:flex-row divide-y lg:divide-y-0 divide-gray-300">
			{isDesktop ? (
				<div
					className={classNames(
						'flex-none overflow-hidden transition-[width] duration-300 ease-out',
						showFilters ? 'lg:w-1/5 xl:w-1/6' : 'lg:w-0 xl:w-0'
					)}
				>
					<div
						className={classNames(
							'absolute top-0 left-0 h-full w-1/5 xl:w-1/6 lg:z-10 transition-transform duration-300 ease-out',
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
			<div className="grow lg:overflow-x-auto lg:border-l lg:border-gray-300">
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
