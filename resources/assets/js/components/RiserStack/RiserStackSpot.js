import React from 'react';
import classNames from "../../classNames";

const RiserStackSpot = ({ cx, cy, radius, enableTarget, onClick, editing, children }) => (
	<svg x={cx - radius} y={cy - radius} className={classNames('overflow-visible', editing && (enableTarget || children) ? 'cursor-pointer' : '')}>
	{children ||
		<circle
			cx={radius}
			cy={radius}
			r={radius}
			style={
				enableTarget ? {
					fill: 'var(--color-purple-400)',
					fillOpacity: 0.5,
					stroke: 'var(--color-purple-500)',
					strokeWidth: '2px',
				} : {
					fill: 'var(--color-purple-400)',
					stroke: 'transparent',
					strokeWidth: '2px',
				}
			}
			onClick={onClick}
		/>
	}
	</svg>
);

export default RiserStackSpot;