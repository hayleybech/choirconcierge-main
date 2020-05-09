<template>
    <div class="riser-stack">

        <form class="riser-settings form-inline">
            <div class="form-group">
                <label for="riser_email">Email</label>
                <input id="riser_email" name="email" type="text" class="form-control mx-3" v-model:value="email">
            </div>
            <div class="form-group">
                <label for="riser_name">Singer Name</label>
                <input id="riser_name" name="singer_name" type="text" class="form-control mx-3" v-model:value="singer">
            </div>
            <div class="form-group">
                <label for="riser_rows">Rows</label>
                <input id="riser_rows" name="rows" type="number" min="1" max="8" class="form-control mx-3" v-model:value="rows">
            </div>

            <div class="form-group">
                <label for="riser_cols">Columns</label>
                <input id="riser_cols" name="cols" type="number" min="1" max="8" class="form-control mx-3" v-model:value="cols">
            </div>

            <div class="form-group">
                <label for="riser_singers">Singers</label>
                <input id="riser_singers" name="singers" type="number" min="1" max="150" class="form-control mx-3" v-model:value="singers">
            </div>

            <div class="form-group">
                <a href="#" id="riser_settings_submit" class="btn btn-primary">Update</a>
            </div>
        </form>

        <p>
            Email: <span>{{email}}</span>
        </p>

        <riser-face v-bind:email="email" v-bind:singer_name="singer"></riser-face>

    </div>
</template>

<script>
    import {RiserFrame} from "../risers/RiserFrame";

    export default {
        name: "RiserStack",
        data() {
            return {
                rows: 4,
                cols: 4,
                singers: 20,
                email: 'test@test.com',
                singer: 'John Smith',
                r: new RiserFrame('#risers', 500, 1000, 4, 4, 20)
            };
        },
        mounted() {
            this.redraw();
        },
        updated() {
            this.redraw();
        },
        methods: {
            redraw() {
                this.r.destroy();
                this.r = new RiserFrame('#risers', 500, 1000, this.rows, this.cols, this.singers);
                this.r.draw();
            }
        }
    }
</script>

<style scoped>

</style>