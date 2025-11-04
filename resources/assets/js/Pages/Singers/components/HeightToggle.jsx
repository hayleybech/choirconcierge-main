import { Switch } from '@headlessui/react';
import React, { useState } from 'react';
import classNames from '../../../classNames';

export const HeightToggle = props => {
	const [showImperial, setShowImperial] = useState(false);
	const imperial = cmToInFt(props.cm);

	return (
		<div className="flex gap-2 items-center flex-wrap">
			<div className="shrink-0">
				{showImperial ? `${imperial.feet} ft ${imperial.inches} in` : `${Math.round(props.cm)} cm`}
			</div>
			<Switch.Group>
				<div className="flex items-center gap-2">
					<Switch.Label className="text-xs font-medium text-gray-700">Metric</Switch.Label>
					<Switch
						checked={showImperial}
						onChange={setShowImperial}
						className={classNames(
							showImperial ? 'bg-purple-600' : 'bg-gray-200',
							'relative inline-flex shrink-0 h-4 w-8 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500'
						)}
					>
						<span
							aria-hidden="true"
							className={classNames(
								showImperial ? 'translate-x-4' : 'translate-x-0',
								'pointer-events-none inline-block h-3 w-3 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200'
							)}
						/>
					</Switch>
					<Switch.Label className="text-xs font-medium text-gray-700">Imperial</Switch.Label>
				</div>
			</Switch.Group>
		</div>
	);
};

export default HeightToggle;

export const cmToInFt = cm => {
	const inches = Math.round(cm / 2.54);
	return {
		feet: Math.floor(inches / 12),
		inches: inches % 12,
	};
};

export const inFtToCm = inFt => {
	return Math.floor(inFt.feet * 2.54 * 12 + inFt.inches * 2.54);
};
