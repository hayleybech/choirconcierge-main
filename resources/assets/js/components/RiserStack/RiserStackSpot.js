import React from 'react';
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../../tailwind.config";
import classNames from "../../classNames";

const RiserStackSpot = ({ cx, cy, radius, enableTarget, onClick, editing, children }) => {
	const fullConfig = resolveConfig(tailwindConfig);

	return (
		<svg x={cx - radius} y={cy - radius} className={classNames('overflow-visible', editing && (enableTarget || children) ? 'cursor-pointer' : '')}>
		{children ||
			<circle
				cx={radius}
				cy={radius}
				r={radius}
				style={
					enableTarget ? {
						fill: fullConfig.theme.colors.purple[400],
						fillOpacity: 0.5,
						stroke: fullConfig.theme.colors.purple[500],
						strokeWidth: '2px',
					} : {
						fill: fullConfig.theme.colors.purple[400],
						stroke: 'transparent',
						strokeWidth: '2px',
					}
				}
				onClick={onClick}
			/>
		}
		</svg>
	);
}

export default RiserStackSpot;