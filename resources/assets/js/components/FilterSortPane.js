import React from 'react';
import Icon from './Icon';
import { useMediaQuery } from 'react-responsive';
import ButtonV2 from './inputs/ButtonV2';

const FilterSortPane = ({ sorts, filters, closeFn, showSortsOnDesktop = false }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	return (
		<div className="flex h-full min-h-0 flex-col relative overflow-y-auto">
			<div className="pt-2 pr-2 flex justify-end items-center absolute right-0">
				<ButtonV2 onClick={closeFn} weight="clear" size="sm">
					<Icon icon="times" />
				</ButtonV2>
			</div>
			<div className="flex-1 border-b border-gray-300 bg-white p-4">
				{(!isDesktop || showSortsOnDesktop) && sorts}
				{filters}
			</div>
		</div>
	);
};

export default FilterSortPane;
