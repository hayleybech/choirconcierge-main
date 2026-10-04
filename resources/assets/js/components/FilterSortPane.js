import React from 'react';
import Button from './inputs/Button';
import Icon from './Icon';
import { useMediaQuery } from 'react-responsive';

const FilterSortPane = ({ sorts, filters, closeFn, showSortsOnDesktop = false }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	return (
		<div className="flex h-full min-h-0 flex-col relative overflow-y-auto">
			<div className="pt-2 pr-2 flex justify-end items-center absolute right-0">
				<Button onClick={closeFn} variant="clear" size="xs">
					<Icon icon="times" />
				</Button>
			</div>
			<div className="flex-1 border-b border-gray-300 bg-white p-4">
				{(!isDesktop || showSortsOnDesktop) && sorts}
				{filters}
			</div>
		</div>
	);
};

export default FilterSortPane;
