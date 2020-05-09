import {RiserFrame} from "./RiserFrame";

export class Risers
{
    private readonly el_field_rows: HTMLInputElement;
    private readonly el_field_cols: HTMLInputElement;
    private readonly el_field_singers: HTMLInputElement;

    private riser_frame: RiserFrame;

    public constructor() {
        this.el_field_rows = <HTMLInputElement> document.querySelector('#riser_rows')!;
        this.el_field_cols = <HTMLInputElement> document.querySelector('#riser_cols')!;
        this.el_field_singers = <HTMLInputElement> document.querySelector('#riser_singers')!;

        this.riser_frame = new RiserFrame('#risers', 500, 1000, 4, 4, 20);
        this.riser_frame.draw();
    }

    update(){
        this.riser_frame.destroy();

        const rows = Number(this.el_field_rows.value);
        const cols = Number(this.el_field_cols.value);
        const singers = Number(this.el_field_singers.value);
        this.riser_frame = new RiserFrame('#risers', 500, 1000, rows, cols, singers);
        this.riser_frame.draw();
    }
}