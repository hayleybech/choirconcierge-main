import {ArcMath} from './ArcMath';
import {XY} from './ArcMath';

export class Arc
{
    // Polar data
    private readonly centre: XY;
    private readonly start_angle: number;
    private readonly end_angle: number;
    private readonly radius: number;

    // Cartesian data
    private readonly cartesian: CartesianArc;

    constructor(centre: XY, radius: number, start_angle: number, end_angle: number)
    {
        this.centre = centre;
        this.radius = radius;
        this.start_angle = start_angle;
        this.end_angle = end_angle;

        this.cartesian = this.toCartesian();
    }

    toCartesian(): CartesianArc
    {
        return {
            start: ArcMath.polarToCartesian(this.centre, this.radius, this.end_angle),
            end: ArcMath.polarToCartesian(this.centre, this.radius, this.start_angle),
            radius: this.radius,
            large_arc_flag: this.end_angle - this.start_angle <= 180 ? "0" : "1"
        }
    }

    getCartesian()
    {
        return this.cartesian;
    }

    describe(): string
    {
        return ArcMath.describeArcCartesian( this.cartesian.start, this.cartesian.radius, this.cartesian.large_arc_flag, this.cartesian.end);
    }

    toString(): string
    {
        return this.describe();
    }
}

interface CartesianArc {
    start: XY;
    end: XY;
    radius: number;
    large_arc_flag: "0" | "1";
}