// @see https://stackoverflow.com/questions/5736398/how-to-calculate-the-svg-path-for-an-arc-of-a-circle
export class ArcMath
{
    static describeArcCartesian( start: XY, radius: number, large_arc_flag: "0"|"1", end: XY ): string
    {
        return [
            "M", start.x, start.y,
            "A", radius, radius, 0, large_arc_flag, 0, end.x, end.y
        ].join(" ");
    }

    static polarToCartesian(center: XY, radius: number, angleInDegrees: number): XY
    {
        const angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;

        return {
            x: center.x + (radius * Math.cos(angleInRadians)),
            y: center.y + (radius * Math.sin(angleInRadians))
        };
    }
}

export interface XY {
    x: number;
    y: number;
}