import React, { useCallback, useRef, useState } from 'react';
import Icon from './Icon';
import ButtonV2 from './inputs/ButtonV2';

export const FilterDrawerActionsContext = React.createContext(null);

const FilterDrawer = ({ closeFn, onApply, onCancel, onClearAll, children }) => {
	const [hasRegisteredActions, setHasRegisteredActions] = useState(false);
	const registeredActions = useRef(null);
	const registerActions = useCallback(actions => {
		registeredActions.current = actions;
		setHasRegisteredActions(Boolean(actions?.apply));
	}, []);
	const showApplyActions = Boolean(onApply || hasRegisteredActions);
	const clearAllFilters = () => {
		(onClearAll ?? registeredActions.current?.clearAll)?.();
		closeFn();
	};

	return (
		<FilterDrawerActionsContext.Provider value={registerActions}>
			<div className={`relative flex h-full min-h-0 flex-col ${showApplyActions ? '' : 'overflow-y-auto'}`}>
				<div className="pt-2 pr-2 flex justify-end items-center absolute right-0">
					<ButtonV2 onClick={onCancel ?? closeFn} weight="clear" size="sm" aria-label="Close filter drawer">
						<Icon icon="times" />
					</ButtonV2>
				</div>
				<div
					className={`${
						showApplyActions ? 'min-h-0 flex-1 overflow-y-auto' : 'flex-1'
					} border-b border-gray-300 bg-white p-4`}
				>
					{children}
				</div>
				{showApplyActions && (
					<div className="sticky bottom-0 z-10 flex shrink-0 justify-between gap-2 border-t border-gray-200 bg-white p-4">
						<ButtonV2
							intent="primary"
							className="flex-1"
							onClick={() => {
								if (onApply) {
									onApply();
								} else {
									registeredActions.current?.apply();
								}
								closeFn();
							}}
						>
							<Icon icon="check" type="regular" />
							Apply
						</ButtonV2>
						{(onClearAll || registeredActions.current?.clearAll) && (
							<ButtonV2 weight="outline" className="flex-1" onClick={clearAllFilters}>
								<Icon icon="undo" type="regular" />
								Reset to Default
							</ButtonV2>
						)}
					</div>
				)}
			</div>
		</FilterDrawerActionsContext.Provider>
	);
};

export default FilterDrawer;
