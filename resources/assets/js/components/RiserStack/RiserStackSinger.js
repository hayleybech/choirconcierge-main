import React, {useMemo} from "react";
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../../tailwind.config";

const RiserStackSinger = ({ singerId, name, imageUrl, radius, onClick, isSelected }) => {
	const fullConfig = resolveConfig(tailwindConfig);
	const labelHeight = 15;
	const labelWidth = 35;
	const nameOffsetY = 15;

	const namePosition = useMemo( () => ({
		x: radius,
		y: 2 * radius + nameOffsetY,
	}), [radius, radius, radius]);

	const labelPosition = useMemo(() => ({
		x: namePosition.x - labelWidth / 2,
		y: namePosition.y - 12,
		height: labelHeight,
		width: labelWidth,
	}), [namePosition]);


	const singerInitials = useMemo(() =>
		name.split(' ')
			.map(name => name.charAt(0).toUpperCase())
			.join('')
	, [name]);

	return (
		<>
			<circle
				cx={radius}
				cy={radius}
				r={radius}
				style={
					isSelected ? {
						fill: `url(#img_${singerId})`,
						stroke: fullConfig.theme.colors.purple[500],
						strokeWidth: '2px',
					} : {
						fill: `url(#img_${singerId})`,
						stroke: 'transparent',
						strokeWidth: '2px',
					}
				}
				onClick={onClick}
			/>
			<rect
				x={labelPosition.x}
				y={labelPosition.y}
				width={labelPosition.width}
				height={labelPosition.height}
				style={{ fill: '#eee', rx: '10px' }}
			/>
			<text
				x={namePosition.x}
				y={namePosition.y}
				textAnchor="middle"
				style={{ fontSize: '12px', fontWeight: 600 }}
			>
				{ singerInitials }
			</text>
			<defs>
				<pattern
					id={`img_${singerId}`}
					patternUnits="objectBoundingBox"
					patternContentUnits="objectBoundingBox"
					width="1"
					height="1"
				>
					<image xlinkHref={imageUrl} x="0" y="0" width="1" height="1" />
				</pattern>
			</defs>
		</>
	);
}

export default RiserStackSinger;
