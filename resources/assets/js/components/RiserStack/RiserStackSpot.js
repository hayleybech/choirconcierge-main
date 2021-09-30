import React from 'react';
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../../tailwind.config";

const RiserStackSpot = ({ cx, cy, radius, children }) => {
	const fullConfig = resolveConfig(tailwindConfig);

	return (
		<svg x={cx - radius} y={cy - radius} >
		{children ||
			<circle
				cx={radius}
				cy={radius}
				r={radius}
				style={{
					fill: fullConfig.theme.colors.purple[400],
					stroke: 'transparent',
					strokeWidth: '1px',
				}}
			/>
		}
		</svg>
	);
}

export default RiserStackSpot;