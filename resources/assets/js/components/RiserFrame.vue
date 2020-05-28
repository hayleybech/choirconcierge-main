<template>
    <svg :width="width" :height="height">
        <!-- Arcs -->
        <g v-for="(n, col) in cols">
            <path v-for="(n, row) in loop_rows" :d="createArcPath(row, col)" class="risers_frame"></path>
        </g>

        <!-- Edges -->
        <g>
            <line v-for="edge in edges" :x1="edge.start.x" :y1="edge.start.y" :x2="edge.end.x" :y2="edge.end.y" class="risers_frame"></line>
        </g>

        <!-- Spots -->
        <g>
            <!--<drop tag="circle" @drop="onDrop" mode="cut"    v-for="spot in spots" :cx="spot.centre.x" :cy="spot.centre.y" :r="spot.radius" class="riser-spot"></drop>-->
            <riser-spot v-for="(spot, index) in spots" :key="index" :coords="spot"></riser-spot>
        </g>
    </svg>
</template>

<script>
import {Arc} from "../risers/Arc";
import {ArcMath} from "../risers/ArcMath";

export default {
    name: "RiserFrame",
    props: {
        'height': Number,   // SVG height
        'width': Number,    // SVG width
        'rows': Number,     // How many rows? We'll draw (num_rows + 1) arcs vertically.
        'cols': Number,     // How many cols? We'll draw (num_cols) arcs horizontally.
        'singers': Number   // How many singers?
    },
    data() {
        return {
            origin_modifier: {
                x: 0.5,
                y: 1.55
            },
            total_width_deg: 70,    // Total arc width in degrees
        }
    },
    computed: {
        loop_rows() {
            return this.rows + 1;
        },
        origin() {
            return {
                x: this.width * this.origin_modifier.x,
                y: this.height * this.origin_modifier.y
            }
        },
        // Start and final radius for the rows
        risers_start_radius() {
            return this.height *  1.05;
        },
        risers_end_radius() {
            return this.height * 0.4;
        },
        // Distance (radius) between rows
        row_height_radius() {
            return this.risers_end_radius / this.rows;
        },
        // Start angle for cols
        risers_start_angle() {
            return 0 - ( this.total_width_deg / 2 );
        },
        // Column angular width
        col_width_deg() {
            return this.total_width_deg / this.cols;
        },
        edges() {
            let edges = [];
            for(let col = 0; col <= this.cols; col++) {
                const angle = this.risers_start_angle + ( this.col_width_deg * col );

                const xy_start = ArcMath.polarToCartesian(this.origin, this.risers_start_radius, angle );
                const xy_end   = ArcMath.polarToCartesian(this.origin, (this.risers_start_radius + this.risers_end_radius), angle)
                edges.push({
                    start: xy_start,
                    end: xy_end
                });
            }
            return edges;
        },


        /**
         * Start generating spots.
         * Loop through the rows, calculating spots needed per row.
         */
        spots() {
            let spots = [];
            for(let row = 0; row < this.rows; row++)
            {
                const spots_this_row = this.calcNumSpots(row);
                spots = spots.concat( this.createSpotsForRow(row, spots_this_row) );
            }
            return spots;
        }
    },
    methods: {
        createArcPath(row, col) {
            const arc = this.createArc(row, col);
            return arc.toString();
        },
        createArc(row, col)
        {
            // Start point (radius) for current row's arcs
            const row_start_radius = this.risers_start_radius + (row * this.row_height_radius);

            // Start angle for current arc
            const col_start_angle = this.risers_start_angle + ( this.col_width_deg * col );
            const col_end_angle   = col_start_angle + this.col_width_deg;

            // Create the arc
            return new Arc( this.origin, row_start_radius, col_start_angle, col_end_angle );
        },





        /**
         * Calculate the number of spots needed for this row.
         */
        calcNumSpots(row)
        {
            const max_spots_per_row = this.singers / 4;

            // Should the first row have an odd number of spots?
            return ( this.rowNeedsOddSpots(row) ) ? (max_spots_per_row - 1) : max_spots_per_row;
        },
        /**
         * Calculate whether this row needs an odd number of spots.
         * Result is equal to the oddness of the row number,
         * unless flipped by first_row_odd.
         */
        rowNeedsOddSpots(row)
        {
            if(this.first_row_odd) {
                return row % 2 === 0;
            }
            return row % 2 !== 0;
        },

        /**
         * Create spots for a row
         * - Loop through the number of spots required
         * - Draw 2 at a time (1 left, 1 right)
         * - On odd rows, 1 spot straddles the centre point.
         * - On even rows, 2 spots share either side of centre.
         */
        createSpotsForRow(row, spots_this_row)
        {
            const start_angle = this.calcStartAngle(spots_this_row);
            const start_radius = this.calcSpotStartRadius();

            let spots = [];
            for(let i = 0; i < (spots_this_row / 2); i++) {

                const spot_angle = start_angle + (this.calcGapAngle() * i);
                const radius = start_radius + (row * this.row_height_radius);

                // Draw left side
                spots.push( this.createSpot(radius, - spot_angle) );

                // Draw right side - skip first time on rows with odd numbers of spots
                if (spots_this_row % 2 === 0 || i > 0) {
                    spots.push( this.createSpot(radius, spot_angle) );
                }
            }
            return spots;
        },

        /**
         * Calculate the offset from centre for a row
         * Offset every other row to create the "windows" between people
         * (0 for odd numbers)
         */
        calcStartAngle(spots_this_row)
        {
            const min_odd_angle = 0;
            const min_even_angle = (this.calcGapAngle() / 2 );

            if (spots_this_row % 2 === 0) {
                return min_even_angle;
            }
            return min_odd_angle;
        },

        /**
         * Calculate the lateral distance between spots, in degrees.
         */
        calcGapAngle()
        {
            const max_spots_per_row = this.singers / this.rows;
            return this.total_width_deg / max_spots_per_row;
        },
        calcSpotStartRadius() {
            return this.risers_start_radius + (this.row_height_radius / 2)
        },

        /**
         * Create a spot at the given position.
         */
        createSpot(pos_radius, pos_angle)
        {
            const pos = ArcMath.polarToCartesian(this.origin, pos_radius, pos_angle);
            const radius = (0.8 * this.row_height_radius) / 2;
            return {
                centre: pos,
                radius: radius
            };
        }
    }
}
</script>

<style scoped>

</style>