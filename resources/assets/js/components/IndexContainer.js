import React from 'react';
import { Fragment, useEffect, useState } from 'react';
import { Transition } from '@headlessui/react';
import { useMediaQuery } from 'react-responsive';
import FilterDialog from './FilterDialog';

const IndexContainer = ({ tableDesktop, tableMobile, emptyState, filterPane, showFilters }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });
	const [isMobileFilterOpen, setIsMobileFilterOpen] = useState(showFilters);

	useEffect(() => {
		setIsMobileFilterOpen(showFilters);
	}, [showFilters]);

	return (
		<div className="flex flex-col lg:flex-row divide-y lg:divide-y-0 lg:divide-x divide-gray-300">
			{isDesktop ? (
				<Transition show={showFilters} as={Fragment}>
					<Transition.Child
						as={Fragment}
						enter="transform transition ease-out duration-300"
						enterFrom="-translate-x-full"
						enterTo="translate-x-0"
						leave="transform transition ease-in duration-200"
						leaveFrom="translate-x-0"
						leaveTo="-translate-x-full"
					>
						<div className="lg:w-1/5 xl:w-1/6 lg:z-10">{filterPane}</div>
					</Transition.Child>
				</Transition>
			) : (
				<FilterDialog isOpen={isMobileFilterOpen} setIsOpen={setIsMobileFilterOpen}>
					{filterPane}
				</FilterDialog>
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
