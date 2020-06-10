<template>
    <div class="riser-stack">

        <div class="riser-settings form-inline" v-if="editDisabled !== true">
            <div class="form-group">
                <label for="riser_rows">Rows</label>
                <input id="riser_rows" name="rows" type="number" min="1" max="8" class="form-control mx-3" v-model.number="rows">
            </div>

            <div class="form-group">
                <label for="riser_cols">Columns</label>
                <input id="riser_cols" name="columns" type="number" min="1" max="8" class="form-control mx-3" v-model.number="cols">
            </div>

            <div class="form-group">
                <label for="riser_front_row_length">Front Row</label>
                <input id="riser_front_row_length" name="front_row_length" type="number" min="1" max="150" class="form-control mx-3" v-model.number="front_row_length">
            </div>

            <div class="form-group">
                <label for="riser_singers">Singers</label>
                <input id="riser_singers" name="singers" type="number" min="1" max="150" class="form-control mx-3" v-model.number="num_singers" disabled>
            </div>

        </div>

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
                <riser-spot v-for="(spot, index) in spots" :key="index" :coords="spot" :singer="spot.singer" :disabled="editDisabled" v-on:addedSinger="addSinger" v-on:removedSinger="removeSinger"></riser-spot>
            </g>
        </svg>

        <input type="hidden" name="singer_positions" :value="JSON.stringify(singers)">

    </div>
</template>

<script>
import {Arc} from "../risers/Arc";
import {ArcMath} from "../risers/ArcMath";

export default {
    name: "RiserStack",
    props: {
        initialRows: { // How many rows? We'll draw (num_rows + 1) arcs vertically.
            type: Number,
            default: 4
        },
        initialCols: { // How many cols? We'll draw (num_cols) arcs horizontally.
            type: Number,
            default: 4
        },
        initialFrontRowLength: {
            type: Number,
            default: 1
        },
        initialSingers: {
            type: Array,
            default: () => []
        },
        editDisabled: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            rows: this.initialRows,
            cols: this.initialCols,
            singers: this.initialSingers,
            front_row_length: this.initialFrontRowLength,
            height: 500,    // SVG height
            width: 1000,     // SVG width

            origin_modifier: {
                x: 0.5,
                y: 1.55
            },
            total_width_deg: 70,    // Total arc width in degrees
            first_row_odd: false
        };
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
         * Calculate the front row
         * All other rows are calculated off this one.
         *
         * Dev: Use Strategy 1a.
         *
         * Strategy 1a - Static: User inputs front row size.
         * Strategy 1b - Static: User inputs the specific singers needed.
         * Strategy 2 - Expanding: Calculate from (numSingersInLongestRow + 2)
         */
        numSpotsForFrontRow()
        {
            return this.front_row_length;
        },

        num_singers()
        {
            const num_odd_rows = Math.floor(this.rows / 2);
            const num_even_rows = Math.ceil(this.rows / 2);

            const singers_odds = this.calcNumSpots(1) * num_odd_rows;
            const singers_evens = this.calcNumSpots(0) * num_even_rows;
            return singers_odds + singers_evens;
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



        addSinger(coords, singer) {
            console.log(singer);
            singer.position = {
                row: coords.row,
                column: coords.column
            };
            this.singers.push(singer);
        },
        removeSinger(coords) {
            const index_to_remove = this.singers.findIndex(function(item){
                return (
                    item.position.row === coords.row &&
                    item.position.column === coords.column
                );
            });
            this.singers.splice( index_to_remove, 1 );
        },
        getSinger(coords) {
            let singer = {
                id: 0,
                name: '',
                email: '',
                part: 0
            }
            this.singers.forEach(function(item){
                if(item.position.row === coords.row
                    && item.position.column === coords.column) {
                    singer = item;
                }
            });
            return singer;
        },


        /**
         * Calculate the number of spots needed for this row.
         */
        calcNumSpots(row)
        {
            if(row % 2 === 0) {
                return this.numSpotsForFrontRow;
            }
            return ( this.numSpotsForFrontRow + 1);
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
            const gap_angle = this.calcGapAngle();
            const offset_angle = this.calcOffsetAngle(spots_this_row);
            const start_radius = this.calcSpotStartRadius();
            const radius = start_radius + (row * this.row_height_radius);

            let spots = [];

            // Draw center (for odd rows)
            if(spots_this_row % 2 !== 0) {
                spots.push( this.createSpot(radius, offset_angle, row, 0) );
            }

            // Draw non-center spots
            for(let i = 1; i <= Math.floor(spots_this_row / 2); i++) {

                const spot_angle = offset_angle + (gap_angle * i);

                // Draw left side
                spots.push( this.createSpot(radius, - spot_angle, row, -i) );

                // Draw right side
                spots.push( this.createSpot(radius, spot_angle, row, i) );
            }
            return spots;
        },

        /**
         * Calculate the offset from centre for a row
         * Offset every other row to create the "windows" between people
         * (0 for odd numbers)
         */
        calcOffsetAngle(spots_this_row)
        {
            const min_odd_angle = 0;
            const min_even_angle = - (this.calcGapAngle() / 2 );

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
            const max_spots_per_row = this.calcNumSpots(1);
            return this.total_width_deg / max_spots_per_row;
        },
        calcSpotStartRadius() {
            return this.risers_start_radius + (this.row_height_radius / 2)
        },

        /**
         * Create a spot at the given position.
         */
        createSpot(pos_radius, pos_angle, row, column)
        {
            const pos = ArcMath.polarToCartesian(this.origin, pos_radius, pos_angle);
            const radius = (0.8 * this.row_height_radius) / 2;
            const singer = this.getSinger({row: row, column: column});
            return {
                centre: pos,
                radius: radius,
                row: row,
                column: column,
                singer: singer
            };
        }
    }
}
</script>

<style scoped>

</style>