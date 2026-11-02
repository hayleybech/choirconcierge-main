import React from 'react';
import Icon from './Icon';
import { useMediaQuery } from 'react-responsive';
import ButtonV2 from './inputs/ButtonV2';
 
const FilterSortPane = ({ sorts, filters, closeFn, showSortsOnDesktop = false, onApply, onCancel }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	return (
		<div className={`relative flex h-full min-h-0 flex-col ${onApply ? '' : 'overflow-y-auto'}`}>
			<div className="pt-2 pr-2 flex justify-end items-center absolute right-0">
				<ButtonV2 onClick={closeFn} weight="clear" size="sm">
					<Icon icon="times" />
				</ButtonV2>
			</div>
			<div
				className={`${onApply ? 'min-h-0 flex-1 overflow-y-auto' : 'flex-1'} border-b border-gray-300 bg-white p-4`}
			>
				{isDesktop && showSortsOnDesktop && sorts}
				{filters}
			</div>
			{onApply && (
				<div className="sticky bottom-0 z-10 flex shrink-0 gap-2 border-t border-gray-200 bg-white p-4">
					<ButtonV2 className="flex-1" onClick={onCancel ?? closeFn}>
						Cancel
					</ButtonV2>
					<ButtonV2 intent="primary" className="flex-1" onClick={onApply}>
						Apply
					</ButtonV2>
				</div>
			)}
		</div>
	);
};

export default FilterSortPane;
