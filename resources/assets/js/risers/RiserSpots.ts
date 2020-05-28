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
    private readonly num_singers: number;
    private readonly first_row_odd: boolean;
    private readonly gap_angle: number;

    constructor(snap: Snap.Paper, origin: XY, riser_start_radius: number, row_height_radius: number, total_width_deg: number, num_rows: number, first_row_odd: boolean, singers: number) {
        this.snap = snap;
        this.origin = origin;
        this.start_radius = riser_start_radius + (row_height_radius / 2);
        this.row_height_radius = row_height_radius;
        this.total_width_deg = total_width_deg;
        this.num_rows = num_rows;
        this.spot_radius = (0.8 * this.row_height_radius) / 2;
        this.first_row_odd = first_row_odd;
        this.num_singers = singers;
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
    calcNumSpots(row: number): number
    {
        const max_spots_per_row = this.num_singers / 4;

        // Should the first row have an odd number of spots?
        return ( this.rowNeedsOddSpots(row) ) ? (max_spots_per_row - 1) : max_spots_per_row;
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
        // draw circle
        // we need a real html element at this position,
        // but we need to draw the circle first to get its screen position
        const pos = ArcMath.polarToCartesian(this.origin, radius, angle);
        const circle = this.snap.circle(pos.x, pos.y, this.spot_radius);

        // get circle box relative to viewport
        /*const circle_bcr = circle.node.getBoundingClientRect();

        // get parent box relative to viewport
        // @todo store riser spots selector in a property
        let parent = document.getElementById('risers-spots');
        const parent_bcr = parent?.getBoundingClientRect();

        // calc circle pos relative to parent
        const circle_rect = {
            height: circle_bcr.height,
            width: circle_bcr.width,
            x: circle_bcr?.x - parent_bcr?.x,
            y: circle_bcr?.y - parent_bcr?.y
        };

        // create the html element
        let tag = document.createElement('div');
        tag.style.height = circle_rect.height + 'px';
        tag.style.width = circle_rect.width + 'px';
        tag.style.left = circle_rect.x + 'px';
        tag.style.top = circle_rect.y + 'px';
        tag.classList.add('riser-spot-red');
        parent?.appendChild(tag);*/
    }

    /**
     * Calculate the lateral distance between spots, in degrees.
     */
    calcGapAngle(): number
    {
        const max_spots_per_row = this.num_singers / this.num_rows;
        return this.total_width_deg / max_spots_per_row;
    }
}