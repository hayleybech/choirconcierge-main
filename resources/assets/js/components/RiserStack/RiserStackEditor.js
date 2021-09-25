import React from 'react';
import RiserStackFrame from "./RiserStackFrame";

const RiserStackEditor = ({ width, height, rows, columns, frontRowLength, frontRowOnFloor}) => (
    <div>
        <RiserStackFrame
            width={parseInt(width)}
            height={parseInt(height)}
            rows={parseInt(rows)}
            columns={parseInt(columns)}
            frontRowOnFloor={frontRowOnFloor}
        />

        {/*<RiserStackSpots*/}
        {/*    rows={rows}*/}
        {/*    frontRowLength={frontRowLength}*/}
        {/*    totalWidthDeg={totalWidthDeg}*/}
        {/*    origin={origin}*/}
        {/*    risersStartRadius={risersStartRadius}*/}
        {/*    editDisabled={editDisabled}*/}

        {/*/>*/}

        {/*<RiserStackHoldingArea voiceParts={voiceParts} />*/}
</div>
);

export default RiserStackEditor;