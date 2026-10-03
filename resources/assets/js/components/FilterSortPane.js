import React from 'react';
import Button from './inputs/Button';
import Icon from './Icon';
import { useMediaQuery } from 'react-responsive';

const FilterSortPane = ({ sorts, filters, closeFn, showSortsOnDesktop = false }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	return (
		<div className="flex h-full flex-col">
			<div className="bg-white pt-2 pr-2 -mb-2 flex justify-end items-center">
				<Button onClick={closeFn} variant="clear" size="xs">
					<Icon icon="times" />
				</Button>
			</div>
			<div className="flex-1 overflow-y-auto border-b border-gray-300 bg-white p-4">
				{(!isDesktop || showSortsOnDesktop) && sorts}
				{filters}
			</div>
		</div>
	);
};

export default FilterSortPane;
