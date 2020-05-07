// An extra line or two to get SnapSVG to play nicely with TypeScript
// see https://stackoverflow.com/questions/47735087/snap-svg-with-typescript-and-webpack-error-cannot-read-property-on-of-undefin
import "snapsvg-cjs" // only once, eg in main.ts
import * as SNAPSVG_TYPE from "snapsvg";
declare var Snap: typeof SNAPSVG_TYPE;
import {Arc} from './Arc';
import {ArcMath, XY} from './ArcMath';
import {RiserSpots} from "./RiserSpots";

export class Risers {
    private readonly snap: Snap.Paper;

    // The full canvas size - the risers themselves only use part of the canvas
    private readonly canvas_height: number = 500;
    private readonly canvas_width: number = 1000;

    // The origin point for the concentric arcs.
    private readonly origin: XY;

    // Given the canvas size, we'll calculate the origin using the modifier
    private readonly origin_modifier: XY = {
        x: 0.5,
        y: 1.55
    };

    // Riser sizing
    private readonly total_width_deg = 70;  // Total arc width in degrees
    private readonly num_rows = 5;          // How many rows? We'll draw (num_rows + 1) arcs vertically.
    private readonly num_cols = 4;          // How many rows? We'll draw (n_cols) arcs horizontally.
    private readonly risers_start_radius: number;
    private readonly risers_end_radius: number;
    private readonly risers_start_angle: number;
    private readonly row_height_radius: number;
    private readonly col_width_deg: number;

    // The vertical lines
    private readonly edges: Line[];

    constructor(selector: string, height: number, width: number) {
        this.snap = Snap(selector);

        this.canvas_height = height;
        this.canvas_width = width;

        this.origin = {
            x: this.canvas_width * this.origin_modifier.x,
            y: this.canvas_height * this.origin_modifier.y
        };

        this.edges = new Array(this.num_cols + 1 ).fill(null).map(emptyLine);

        // Start and radius for the rows
        this.risers_start_radius = this.canvas_height *  1.05;
        this.risers_end_radius = this.canvas_height * 0.4;

        // Distance (radius) between rows
        this.row_height_radius = this.risers_end_radius / this.num_rows;

        // Start angle for cols
        this.risers_start_angle =  0 - ( this.total_width_deg / 2 );
        this.col_width_deg = this.total_width_deg / this.num_cols;
    }

    draw(): void
    {
        for(let col = 0; col < this.num_cols; col++)
        {
            for(let row = 0; row <= this.num_rows; row++)
            {
                const arc = this.createArc(row, col);

                const arcPath = this.snap.path( arc.toString() );
                arcPath.addClass('risers_frame');

                this.calcCellEdges(row, col, arc);
            }
        }

        this.drawEdges();

        let spots = new RiserSpots(this.snap, this.origin, this.risers_start_radius, this.row_height_radius, this.total_width_deg, this.num_rows, false);
        spots.draw();
    }

    drawEdges(): void
    {
        for(let i = 0; i <= this.num_cols; i++)
        {
            const edge = this.edges[i];
            const line = this.snap.line(
                edge.start!.x, edge.start!.y,
                edge.end!.x, edge.end!.y
            );
            line.addClass('risers_frame');
        }
    }

    createArc(row: number, col: number): Arc
    {
        // Start point (radius) for current row's arcs
        const row_start_radius = this.risers_start_radius + (row * this.row_height_radius);

        // Start angle for current arc
        const col_start_angle = this.risers_start_angle + ( this.col_width_deg * col );
        const col_end_angle   = col_start_angle + this.col_width_deg;

        // Create the arc
        return new Arc( this.origin, row_start_radius, col_start_angle, col_end_angle );
    }

    calcCellEdges(row: number, col: number, arc: Arc): void
    {
        const arc_l = arc.getCartesian().end;
        const arc_r = arc.getCartesian().start

        // Save left edges
        if(row === 0) {
            this.edges[col].start = arc_l;
        }
        else if(row === this.num_rows) {
            this.edges[col].end =  arc_l;
        }

        // Save far right edge
        if( col === (this.num_cols - 1) ) {

            if(row === 0) {
                this.edges[col+1].start = arc_r;
            }
            else if( row === this.num_rows ) {
                this.edges[col+1].end = arc_r;
            }
        }
    }
}
interface Line {
    start: XY|null;
    end: XY|null;
}

// https://stackoverflow.com/questions/49741673/typescript-initiate-an-empty-interface-object
const emptyLine = (): Line => ({
    start: null,
    end: null
});