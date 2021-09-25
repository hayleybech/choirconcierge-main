import React, {useMemo} from 'react';
import {ArcMath} from "../../risers/ArcMath";
import resolveConfig from 'tailwindcss/resolveConfig'
import tailwindConfig from '../../../../../tailwind.config'

const RiserStackSpots = ({ rows, spotsOnFrontRow, totalAngularWidth, risersStartRadius, rowHeightAlongRadius, origin }) => {
    const fullConfig = resolveConfig(tailwindConfig);
    const spots = useMemo(() => createSpots(rows), [rows, spotsOnFrontRow, totalAngularWidth, risersStartRadius, rowHeightAlongRadius, origin]);

    function createSpots(rows) {
        let spots = [];
        for (let row = 0; row < rows; row++) {
            spots = spots.concat(createSpotsForRow(row, calcNumSpotsForRow(row)));
        }
        return spots;
    }

    function calcNumSpotsForRow(row) {
        if (row % 2 === 0) {
            return spotsOnFrontRow;
        }
        return spotsOnFrontRow + 1;
    }

    /**
     * Create spots for a row
     * - Loop through the number of spots required
     * - Draw 2 at a time (1 left, 1 right)
     * - On odd rows, 1 spot straddles the centre point.
     * - On even rows, 2 spots share either side of centre.
     */
    function createSpotsForRow(row, spotsOnThisRow) {
        const angularGapBetweenSpots = calcGapAngle();
        const startRadius = calcSpotStartRadius();
        const radius = startRadius + row * rowHeightAlongRadius;
        const middleColumn = spotsOnThisRow / 2 - 0.5;

        let spots = [];

        for (let col = 0; col < spotsOnThisRow; col++) {
            const distanceFromMiddle = col - middleColumn;
            const angularSpotPosition = angularGapBetweenSpots * distanceFromMiddle;
            spots.push(createSpotAtPosition(radius, angularSpotPosition, row, col));
        }
        return spots;
    }

    /**
     * Calculate the lateral distance between spots, in degrees.
     */
    function calcGapAngle() {
        const maxSpotsPerRow = calcNumSpotsForRow(1);
        return totalAngularWidth / maxSpotsPerRow;
    }

    function calcSpotStartRadius() {
        return risersStartRadius + rowHeightAlongRadius / 2;
    }

    function createSpotAtPosition(positionRadius, positionAngle, row, column) {
        const pos = ArcMath.polarToCartesian(origin, positionRadius, positionAngle);
        const radius = (0.8 * rowHeightAlongRadius) / 2;
        // const singer = this.$parent.getSinger({ row: row, column: column });
        return {
            centre: pos,
            radius: radius,
            row: row,
            column: column,
            // singer: singer,
        };
    }

    return (
        <g>
            {spots.map((spot, key) => (
                <svg key={key}>
                    <circle
                        cx={spot.centre.x}
                        cy={spot.centre.y}
                        r={spot.radius}
                        style={{
                            fill: fullConfig.theme.colors.purple[400],
                            stroke: 'transparent',
                            strokeWidth: '1px',
                        }}
                    />
                </svg>
            ))}
        </g>
    );
}

export default RiserStackSpots;