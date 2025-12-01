import React, { useState } from 'react';
import classNames from '../../classNames';
import { Switch } from '@headlessui/react';
import TextInput from './TextInput';
import { cmToInFt, inFtToCm } from '../../Pages/Singers/components/HeightToggle';

const MetricImperialInput = ({ name, value, updateFn, hasErrors, wrapperClasses, disabled }) => {
	const [showImperial, setShowImperial] = useState(false);

	const initialImperial = Number.isNaN(value) ? '' : cmToInFt(value);
	const [valuesImperial, setValuesImperial] = useState(initialImperial);

	return (
		<div className={`mt-1 ${wrapperClasses}`}>
			<div className="flex gap-2 justify-between items-end">
				{showImperial ? (
					<div className="flex gap-1 items-end">
						<TextInput
							name={`${name}-ft`}
							type="number"
							value={initialImperial.feet}
							updateFn={ftNew => {
								setValuesImperial(old => ({
									inches: old.inches,
									feet: ftNew,
								}));
								updateFn(
									inFtToCm({
										inches: valuesImperial.inches,
										feet: ftNew,
									})
								);
							}}
							wrapperClasses="w-14"
							hasErrors={hasErrors}
							disabled={disabled}
						/>
						<span className="font-sm text-gray-700">ft</span>

						<TextInput
							name={`${name}-in`}
							type="number"
							value={initialImperial.inches}
							updateFn={inNew => {
								setValuesImperial(old => ({
									inches: inNew,
									feet: old.feet,
								}));
								updateFn(inFtToCm({
									inches: inNew,
									feet: valuesImperial.feet,
								}));
							}}
							wrapperClasses="w-16"
							hasErrors={hasErrors}
							disabled={disabled}
						/>
						<span className="font-sm text-gray-700">in</span>
					</div>
				) : (
					<div className="flex gap-1 items-end">
						<TextInput
							name={name}
							type="number"
							value={Math.floor(value)}
							updateFn={updateFn}
							wrapperClasses="w-24"
							hasErrors={hasErrors}
							disabled={disabled}
						/>
						<span className="font-sm text-gray-700">cm</span>
					</div>
				)}
				<Switch.Group>
					<div className={`flex items-center gap-2 ${disabled && 'opacity-50'}`}>
						<Switch.Label className="text-xs font-medium text-gray-700">Metric</Switch.Label>
						<Switch
							checked={showImperial}
							onChange={setShowImperial}
							disabled={disabled}
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
		</div>
	);
};

export default MetricImperialInput;
