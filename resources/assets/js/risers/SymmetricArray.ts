export class SymmetricArray<T>
{
    private left: T[];
    private mid: T|null;
    private right: T[];

    constructor() {
        this.left = Array();
        this.mid = null;
        this.right = Array();
    }

    public set(index: number, value: T): void {
        if(index > 0) {
            this.right.splice(index, 1, value);
            return;
        }
        if(index < 0) {
            this.left.splice(Math.abs(index), 1, value);
            return;
        }
        this.mid = value;
    }

    public get(index: number): T|null {
        if(index > 0) {
            return this.right[index];
        }
        if(index < 0) {
            return this.left[Math.abs(index)];
        }
        return this.mid;
    }

}