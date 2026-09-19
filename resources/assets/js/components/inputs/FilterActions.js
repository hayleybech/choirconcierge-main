import React from 'react';

const FilterActions = ({ onSelectAll, onClear }) => {
	return (
		<div className="flex items-center space-x-2">
			{onSelectAll && (
				<>
					<button
						type="button"
						onClick={onSelectAll}
						className="text-xs text-purple-600 hover:text-purple-800 font-medium underline hover:no-underline"
					>
						All
					</button>
					<span className="text-gray-300 text-xs">|</span>
				</>
			)}
			<button
				type="button"
				onClick={onClear}
				className="text-xs text-purple-600 hover:text-purple-800 font-medium underline hover:no-underline"
			>
				Clear
			</button>
		</div>
	);
};

export default FilterActions;
