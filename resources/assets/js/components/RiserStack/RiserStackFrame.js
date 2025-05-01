import React, {useMemo} from 'react';
import {Arc} from "../../risers/Arc";
import {ArcMath} from "../../risers/ArcMath";
import resolveConfig from 'tailwindcss/resolveConfig'
import tailwindConfig from '../../../../../tailwind.config'

const RiserStackFrame = ({
    columns,
    rows,
    frontRowOnFloor,
    origin,
    totalAngularWidth,
    frameStartAngle,
    frameStartRadius,
    frameEndRadius,
    rowHeightAlongRadius,
}) => {
    const fullConfig = resolveConfig(tailwindConfig);
    const columnAngularWidth = useMemo(() => totalAngularWidth / columns, [columns]);

    const arcs = useMemo(() => createArcs(), [rows, columns, frontRowOnFloor]);
    const edges = useMemo(() => createEdges(), [columns, frontRowOnFloor]);
    
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
        const rowStartRadius = frameStartRadius + row * rowHeightAlongRadius;

        // Start angle for current arc
        const columnStartAngle = frameStartAngle + columnAngularWidth * column;
        const columnEndAngle = columnStartAngle + columnAngularWidth;

        // Create the arc
        return new Arc(origin, rowStartRadius, columnStartAngle, columnEndAngle).toString();
    }

    function createEdges() {
        let edges = [];

        let edgeStartRadius = frameStartRadius;
        let edgeEndRadius = frameStartRadius + frameEndRadius;

        // Support front row on floor (invisible first row)
        if (frontRowOnFloor) {
            edgeStartRadius += rowHeightAlongRadius;
        }

        for (let column = 0; column <= columns; column++) {
            const angle = frameStartAngle + columnAngularWidth * column;

            const xyStart = ArcMath.polarToCartesian(origin, edgeStartRadius, angle);
            const xyEnd = ArcMath.polarToCartesian(origin, edgeEndRadius, angle);
            edges.push({
                start: xyStart,
                end: xyEnd,
            });
        }
        return edges;
    }

    return (
        <g>
            <g>
            {arcs.map((arc, key) => (
                <path key={key} d={arc} style={{ fill: 'none', stroke: fullConfig.theme.colors.gray[500], strokeWidth: 1 }} />
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
                        style={{ fill: 'none', stroke: fullConfig.theme.colors.gray[500], strokeWidth: 1 }}
                    />
                ))}
            </g>
        </g>
    );
}

export default RiserStackFrame;