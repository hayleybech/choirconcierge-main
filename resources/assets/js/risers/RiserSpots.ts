import {ArcMath, XY} from './ArcMath';
import * as SNAPSVG_TYPE from "snapsvg";
declare var Snap: typeof SNAPSVG_TYPE;

export class RiserSpots {
    private readonly snap: Snap.Paper;

    private readonly origin: XY;
    private readonly start_radius: number;
    private readonly row_height_radius: number;
    private readonly spot_radius: number;
    private readonly total_width_deg: number;
    private readonly num_rows: number;
    private readonly first_row_odd: boolean;
    private readonly gap_angle: number;

    constructor(snap: Snap.Paper, origin: XY, riser_start_radius: number, row_height_radius: number, total_width_deg: number, num_rows: number, first_row_odd: boolean) {
        this.snap = snap;
        this.origin = origin;
        this.start_radius = riser_start_radius + (row_height_radius / 2);
        this.row_height_radius = row_height_radius;
        this.total_width_deg = total_width_deg;
        this.num_rows = num_rows;
        this.spot_radius = (0.8 * this.row_height_radius) / 2;
        this.first_row_odd = first_row_odd;
        this.gap_angle = this.calcGapAngle();
    }

    /**
     * Start drawing spots.
     * Loop through the rows, calculating spots needed per row.
     */
    draw(): void
    {
        for(let i = 0; i < this.num_rows; i++)
        {
            let spots_this_row = this.calcNumSpots(i);

            this.drawSpotsForRow(i, spots_this_row);
        }
    }

    /**
     * Calculate the number of spots needed for this row.
     */
    calcNumSpots(row: number): 9|10
    {
        // Should the first row have an odd number of spots?
        return ( this.rowNeedsOddSpots(row) ) ? 9 : 10;
    }

    /**
     * Calculate whether this row needs an odd number of spots.
     * Result is equal to the oddness of the row number,
     * unless flipped by first_row_odd.
     */
    rowNeedsOddSpots(row: number): boolean
    {
        if(this.first_row_odd) {
            return row % 2 == 0;
        }
        return row % 2 != 0;
    }

    /**
     * Create spots for a row
     * - Loop through the number of spots required
     * - Draw 2 at a time (1 left, 1 right)
     * - On odd rows, 1 spot straddles the centre point.
     * - On even rows, 2 spots share either side of centre.
     */
    drawSpotsForRow(row: number, spots_this_row: number)
    {
        const start_angle = this.calcStartAngle(spots_this_row);

        for(let i = 0; i < (spots_this_row / 2); i++) {

            const spot_angle = start_angle + (this.gap_angle * i);
            const radius = this.start_radius + (row * this.row_height_radius);

            // Draw left side
            this.drawSpot(radius, - spot_angle);

            // Draw right side - skip first time on rows with odd numbers of spots
            if (spots_this_row % 2 === 0 || i > 0) {
                this.drawSpot(radius, spot_angle);
            }
        }

    }

    /**
     * Calculate the offset from centre for a row
     * Offset every other row to create the "windows" between people
     * (0 for odd numbers)
     */
    calcStartAngle(spots_this_row: number): number
    {
        const min_odd_angle = 0;
        const min_even_angle = (this.calcGapAngle() / 2 );

        if (spots_this_row % 2 === 0) {
            return min_even_angle;
        }
        return min_odd_angle;
    }

    /**
     * Draw a spot at the given position.
     */
    drawSpot(radius: number, angle: number): void
    {
        const pos = ArcMath.polarToCartesian(this.origin, radius, angle);
        const circle = this.snap.circle(pos.x, pos.y, this.spot_radius);
        circle.addClass('riser-spot');
    }

    /**
     * Calculate the lateral distance between spots, in degrees.
     */
    calcGapAngle(): number
    {
        const max_spots_per_row = 10;
        return this.total_width_deg / max_spots_per_row;
    }
}