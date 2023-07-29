import React from 'react';
import {ArcMath} from "../../risers/ArcMath";
import RiserStackSpot from "./RiserStackSpot";
import RiserStackSinger from "./RiserStackSinger";

const RiserStackSpots = ({
    editing,
    rows,
    spotsOnFrontRow,
    totalAngularWidth,
    risersStartRadius,
    rowHeightAlongRadius,
    origin,
    singers,
    selectedSinger,
    setSelectedSinger,
    moveSelectedSingerTo,
    showHeights,
}) => {
    const spots = createSpots(rows);

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
        return {
            centre: ArcMath.polarToCartesian(origin, positionRadius, positionAngle),
            radius: (0.8 * rowHeightAlongRadius) / 2,
            row: row,
            column: column,
            singer: getSinger({row: row, column: column}),
        };
    }

    function getSinger(coords) {
        return singers.find(
            	item => item.position.row === coords.row
                && item.position.column === coords.column
        ) || null;
    }

    return (
        <g>
            {spots.map((spot, key) => (
                <RiserStackSpot
                    key={key}
                    cx={spot.centre.x}
                    cy={spot.centre.y}
                    radius={spot.radius}
                    editing={editing}
                    enableTarget={!!selectedSinger}
                    onClick={editing && selectedSinger ? () => moveSelectedSingerTo({ row: spot.row, column: spot.column }) : null}
                >
                    {spot.singer &&
                        <RiserStackSinger
                            singerId={spot.singer.id}
                            name={spot.singer.user.name}
                            imageUrl={spot.singer.user_avatar_thumb_url}
                            radius={spot.radius}
                            onClick={editing && !selectedSinger ? () => setSelectedSinger(spot.singer) : null}
                            isSelected={selectedSinger?.id === spot.singer.id}
                            showHeight={showHeights}
                            singerHeight={Math.round(spot.singer.user.height)}
                        />
                    }
               </RiserStackSpot>
            ))}
        </g>
    );
}

export default RiserStackSpots;