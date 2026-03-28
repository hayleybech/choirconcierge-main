import React from 'react';
import SectionHeader from './SectionHeader';
import SectionTitle from './SectionTitle';
import Button from './inputs/Button';
import Icon from './Icon';
import { useMediaQuery } from 'react-responsive';

const FilterSortPane = ({ sorts, filters, closeFn }) => {
	const isDesktop = useMediaQuery({ query: '(min-width: 1024px)' });

	return (
		<div>
			<div className="bg-white pt-2 pr-2 -mb-2 flex justify-end items-center">
				<Button onClick={closeFn} variant="clear" size="xs">
					<Icon icon="times" />
				</Button>
			</div>
			<div className="bg-white p-4 border-b border-gray-300 h-full">
				{isDesktop || sorts}
				{filters}
			</div>
		</div>
	);
};

export default FilterSortPane;
