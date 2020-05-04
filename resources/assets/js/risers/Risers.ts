// see https://stackoverflow.com/questions/47735087/snap-svg-with-typescript-and-webpack-error-cannot-read-property-on-of-undefin
import "snapsvg-cjs" // only once, eg in main.ts
import * as SNAPSVG_TYPE from "snapsvg";
declare var Snap: typeof SNAPSVG_TYPE;
import {Arc} from './Arc';
import {XY} from './ArcMath';

export class Risers {
    #snap: Snap.Paper;

    // The full canvas size - the risers themselves only use part of the canvas
    readonly #canvas_height: number = 500;
    readonly #canvas_width: number = 1000;

    // The origin point for the concentric arcs.
    #origin: XY;

    // Given the canvas size, we'll calculate the origin using the modifier
    #origin_modifier: XY = {
        x: 0.5,
        y: 1.55
    };

    // Riser sizing
    #total_width_deg = 70;  // Total arc width in degrees
    #num_rows = 4;          // How many rows? We'll draw (num_rows + 1) arcs vertically.
    #num_cols = 3;          // How many rows? We'll draw (n_cols) arcs horizontally.
    readonly #risers_start_radius: number;
    readonly #risers_end_radius: number;
    readonly #risers_start_angle: number;
    readonly #row_height_radius: number;
    readonly #col_width_deg: number;

    // Line styles
    #style = {
        fill: 'none',
        stroke: 'black',
        strokeWidth: 1
    };

    // The vertical lines
    readonly #edges: Line[];

    constructor(selector: string, height: number, width: number) {
        this.#snap = Snap(selector);

        this.#canvas_height = height;
        this.#canvas_width = width;

        this.#origin = {
            x: this.#canvas_width * this.#origin_modifier.x,
            y: this.#canvas_height * this.#origin_modifier.y
        };

        this.#edges = new Array(this.#num_cols + 1 ).fill(null).map(emptyLine);

        // Start and radius for the rows
        this.#risers_start_radius = this.#canvas_height *  1.05;
        this.#risers_end_radius = this.#canvas_height * 0.4;

        // Distance (radius) between rows
        this.#row_height_radius = this.#risers_end_radius / this.#num_rows;

        // Start angle for cols
        this.#risers_start_angle =  0 - ( this.#total_width_deg / 2 );
        this.#col_width_deg = this.#total_width_deg / this.#num_cols;
    }

    draw(): void
    {
        for(let col = 0; col < this.#num_cols; col++)
        {
            for(let row = 0; row <= this.#num_rows; row++)
            {
                const arc = this.createArc(row, col);

                const arcPath = this.#snap.path( arc.toString() );
                arcPath.attr(this.#style);

                this.calcCellEdges(row, col, arc);
            }
        }

        this.drawEdges();
    }

    drawEdges(): void
    {
        for(let i = 0; i <= this.#num_cols; i++)
        {
            const edge = this.#edges[i];
            const line = this.#snap.line(
                edge.start!.x, edge.start!.y,
                edge.end!.x, edge.end!.y
            );
            line.attr(this.#style);
        }
    }

    createArc(row: number, col: number): Arc
    {
        // Start point (radius) for current row's arcs
        const row_start_radius = this.#risers_start_radius + (row * this.#row_height_radius);

        // Start angle for current arc
        const col_start_angle = this.#risers_start_angle + ( this.#col_width_deg * col );
        const col_end_angle   = col_start_angle + this.#col_width_deg;

        // Create the arc
        return new Arc( this.#origin, row_start_radius, col_start_angle, col_end_angle );
    }

    calcCellEdges(row: number, col: number, arc: Arc): void
    {
        const arc_l = arc.getCartesian().end;
        const arc_r = arc.getCartesian().start

        // Save left edges
        if(row === 0) {
            this.#edges[col].start = arc_l;
        }
        else if(row === this.#num_rows) {
            this.#edges[col].end =  arc_l;
        }

        // Save far right edge
        if( col === (this.#num_cols - 1) ) {

            if(row === 0) {
                this.#edges[col+1].start = arc_r;
            }
            else if( row === this.#num_rows ) {
                this.#edges[col+1].end = arc_r;
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