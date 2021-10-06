import React, {useMemo, useState} from 'react';
import RiserStackFrame from "./RiserStackFrame";
import RiserStackSpots from "./RiserStackSpots";

const RiserStackEditor = ({
    width, height, rows, columns, spotsOnFrontRow, frontRowOnFloor, singerPositions, setPositions, selectedSinger, setSelectedSinger, removeSingerFromHoldingArea
}) => {
    const originModifier = {
        x: 0.5,
        y: 1.55,
    };
    const origin = useMemo(() => ({
        x: width * originModifier.x,
        y: height * originModifier.y,
    }), [width, height]);

    const totalAngularWidth = 85;
    const frameStartAngle = 0 - totalAngularWidth / 2;
    const frameStartRadius = useMemo(() => height * 1.05, [height]);
    const frameEndRadius = useMemo(() => height * 0.4, [height]);

    const rowHeightAlongRadius = useMemo(() => frameEndRadius / rows, [frameEndRadius, rows]);

    function moveSingerToStack(coords, singer) {
        removeSingerFromHoldingArea(singer);

        singer.position = {
            row: coords.row,
            column: coords.column,
        };

        singerPositions.push(singer);
        setPositions(singerPositions);

        setSelectedSinger(null);
    }

    return(
        <div>
            <svg width={width} height={400}>

                <RiserStackFrame
                    rows={rows}
                    columns={columns}
                    frontRowOnFloor={frontRowOnFloor}
                    origin={origin}
                    totalAngularWidth={totalAngularWidth}
                    frameStartAngle={frameStartAngle}
                    frameStartRadius={frameStartRadius}
                    frameEndRadius={frameEndRadius}
                    rowHeightAlongRadius={rowHeightAlongRadius}
                />

                <RiserStackSpots
                    rows={rows}
                    spotsOnFrontRow={spotsOnFrontRow}
                    origin={origin}
                    risersStartRadius={frameStartRadius}
                    rowHeightAlongRadius={rowHeightAlongRadius}
                    totalAngularWidth={totalAngularWidth}
                    singers={singerPositions}
                    selectedSinger={selectedSinger}
                    setSelectedSinger={setSelectedSinger}
                    moveSelectedSingerTo={(coords) => moveSingerToStack(coords, selectedSinger)}
                />

            </svg>

        </div>
    );
}

export default RiserStackEditor;