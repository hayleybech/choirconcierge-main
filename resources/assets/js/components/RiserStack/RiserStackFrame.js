import React, {useMemo} from 'react';
import {Arc} from "../../risers/Arc";
import {ArcMath} from "../../risers/ArcMath";

const RiserStackFrame = ({ width, height, columns, rows, frontRowOnFloor }) => {
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

    // Distance (along radius) between rows
    const rowHeight = useMemo(() => frameEndRadius / rows, [frameEndRadius, rows]);
    const columnAngularWidth = useMemo(() => totalAngularWidth / columns, [columns]);

    const arcs = useMemo(() => createArcs(), [height, rows, columns, frontRowOnFloor]);
    const edges = useMemo(() => createEdges(), [height, columns, frontRowOnFloor]);
    
    function createArcs() {
        let arcs = [];
        for(let column = 0; column < columns; column++) {
            for(let row = 0; row < ( rows + 1); row++) {
                if(row === 0 && frontRowOnFloor) {
                    continue;
                }
                arcs.push(createArc(row, column));
            }
        }
        return arcs;
    }

    function createArc(row, column) {
        // Start point (radius) for current row's arcs
        const rowStartRadius = frameStartRadius + row * rowHeight;

        // Start angle for current arc
        const columnStartAngle = frameStartAngle + columnAngularWidth * column;
        const columnEndAngle = columnStartAngle + columnAngularWidth;

        // Create the arc
        return new Arc(origin, rowStartRadius, columnStartAngle, columnEndAngle).toString();
    }

    function createEdges() {
        let edges = [];

        let edge_start_radius = frameStartRadius;
        let edge_end_radius = frameStartRadius + frameEndRadius;

        // Support front row on floor (invisible first row)
        if (frontRowOnFloor) {
            edge_start_radius += rowHeight;
        }

        for (let column = 0; column <= columns; column++) {
            const angle = frameStartAngle + columnAngularWidth * column;

            const xy_start = ArcMath.polarToCartesian(origin, edge_start_radius, angle);
            const xy_end = ArcMath.polarToCartesian(origin, edge_end_radius, angle);
            edges.push({
                start: xy_start,
                end: xy_end,
            });
        }
        return edges;
    }

    return (
        <svg width={width} height={height}>
            <g>
            {arcs.map((arc, key) => (
                <path key={key} d={arc} style={{ fill: 'none', stroke: '#777', strokeWidth: 1 }} />
            ))}
            </g>

            <g>
                {edges.map((edge, key) => (
                    <line
                        key={key}
                        x1={edge.start.x}
                        y1={edge.start.y}
                        x2={edge.end.x}
                        y2={edge.end.y}
                        style={{ fill: 'none', stroke: '#777', strokeWidth: 1 }}
                    />
                ))}
            </g>
        </svg>
    );
}

export default RiserStackFrame;