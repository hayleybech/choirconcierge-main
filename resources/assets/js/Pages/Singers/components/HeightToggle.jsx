import React from 'react';
import useMetricImperialPreference from "../../../hooks/useMetricImperialPreference";

export const HeightToggle = props => {
	const showImperial = useMetricImperialPreference();
	const imperial = cmToInFt(props.cm);

	return (
		<div className="flex gap-2 items-center flex-wrap">
			<div className="shrink-0">
				{showImperial ? `${imperial.feet} ft ${imperial.inches} in` : `${Math.round(props.cm)} cm`}
			</div>
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
