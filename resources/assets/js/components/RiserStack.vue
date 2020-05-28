<template>
    <div class="riser-stack">

        <form class="riser-settings form-inline">
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

        <riser-frame :height="500" :width="1000" :rows="4" :cols="4" :singers="20"></riser-frame>

    </div>
</template>

<script>
    import {Drag, Drop} from "vue-easy-dnd";

    export default {
        name: "RiserStack",
        components: {
            Drag,
            Drop
        },
        data() {
            return {
                rows: 4,
                cols: 4,
                singers: 20,
            };
        },
    }
</script>

<style scoped>

</style>