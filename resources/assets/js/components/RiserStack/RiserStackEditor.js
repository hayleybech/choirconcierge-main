import React, {useMemo} from 'react';
import RiserStackFrame from "./RiserStackFrame";
import RiserStackSpots from "./RiserStackSpots";

const RiserStackEditor = ({ width, height, rows, columns, spotsOnFrontRow, frontRowOnFloor}) => {
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

    return(
        <div>
            <svg width={width} height={height}>

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
                />

            </svg>

            {/*<RiserStackHoldingArea voiceParts={voiceParts} />*/}
        </div>
    );
}

export default RiserStackEditor;