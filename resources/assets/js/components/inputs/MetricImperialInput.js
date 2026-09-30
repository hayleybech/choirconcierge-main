import React, {useState} from 'react';
import TextInput from './TextInput';
import { cmToInFt, inFtToCm } from '../../Pages/Singers/components/HeightToggle';
import useMetricImperialPreference from "../../hooks/useMetricImperialPreference";

const MetricImperialInput = ({ name, value, updateFn, hasErrors, wrapperClasses, disabled }) => {
	const showImperial = useMetricImperialPreference();

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
			</div>
		</div>
	);
};

export default MetricImperialInput;
