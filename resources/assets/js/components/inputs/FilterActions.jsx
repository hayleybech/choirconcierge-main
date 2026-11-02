import React from 'react';
 
const FilterActions = ({ onSelectAll, onClear, onReset, clearLabel = 'Clear', resetDisabled = false, resetAriaLabel = 'Reset' }) => {
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
				{clearLabel}
			</button>
			{onReset && (
				<button
					type="button"
					onClick={onReset}
					disabled={resetDisabled}
					aria-label={resetAriaLabel}
					className="text-xs font-medium text-purple-600 underline hover:text-purple-800 hover:no-underline disabled:cursor-not-allowed disabled:text-gray-400 disabled:no-underline"
				>
					Reset
				</button>
			)}
		</div>
	);
};

export default FilterActions;
