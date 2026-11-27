import React from 'react';
import { linkStyles } from '../StyledLink';

const FilterActions = ({
	onSelectAll,
	onClear,
	onReset,
	selectAllLabel = 'Select All',
	clearLabel = 'Clear',
	resetDisabled = false,
	resetAriaLabel = 'Reset',
}) => {
	return (
		<div className="flex items-center space-x-2">
			{onSelectAll && (
				<>
					<button type="button" onClick={onSelectAll} className={linkStyles('secondary', 'text-xs')}>
						{selectAllLabel}
					</button>
					<span className="text-gray-300 text-xs">|</span>
				</>
			)}
			<button type="button" onClick={onClear} className={linkStyles('secondary', 'text-xs')}>
				{clearLabel}
			</button>
			{onReset && (
				<button
					type="button"
					onClick={onReset}
					disabled={resetDisabled}
					aria-label={resetAriaLabel}
					className={linkStyles(
						'secondary',
						'text-xs disabled:cursor-not-allowed disabled:text-gray-400 disabled:no-underline disabled:hover:text-gray-400'
					)}
				>
					Reset
				</button>
			)}
		</div>
	);
};

export default FilterActions;
