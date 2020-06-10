<template>
    <!-- Spots -->
    <g>
        <riser-spot v-for="(spot, index) in spots" :key="index" :coords="spot" :singer="spot.singer" :disabled="editDisabled"></riser-spot>
    </g>
</template>

<script>
import {ArcMath} from "../risers/ArcMath";

export default {
    name: "RiserLayerSpots",
    props: {
        rows: {
            type: Number,
            default: 4
        },
        frontRowLength: {
            type: Number,
            default: 1
        },
        totalWidthDeg: {
            type: Number,
            required: true
        },
        risersStartRadius: {
            type: Number,
            required: true
        },
        rowHeightRadius: {
            type: Number,
            required: true
        },
        origin: {
            required: true
        },
        editDisabled: {
            type: Boolean,
            default: false
        }
    },
    computed: {
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
        },

    },
    methods: {

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
            const radius = start_radius + (row * this.rowHeightRadius);

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
            return this.totalWidthDeg / max_spots_per_row;
        },

        calcSpotStartRadius() {
            return this.risersStartRadius + (this.rowHeightRadius / 2)
        },

        /**
         * Calculate the number of spots needed for this row.
         */
        calcNumSpots(row)
        {
            if(row % 2 === 0) {
                return this.frontRowLength;
            }
            return ( this.frontRowLength + 1);
        },

        /**
         * Create a spot at the given position.
         */
        createSpot(pos_radius, pos_angle, row, column)
        {
            const pos = ArcMath.polarToCartesian(this.origin, pos_radius, pos_angle);
            const radius = (0.8 * this.rowHeightRadius) / 2;
            const singer = this.$parent.getSinger({row: row, column: column});
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