<template>
    <div class="row">

        <div class="col-md-3">
            <div class="list">
                <drag v-for="n in nums" :data="n" class="item" :key="n" @cut="remove(n)">
                    {{n}}
                </drag>
            </div>
        </div>

        <div class="col-md-3">
            <drop-list
                    :items="items"
                    class="list"
                    @insert="onInsert"
                    @reorder="$event.apply(items)"
                    mode="cut"
            >
                <template v-slot:item="{item}">
                    <drag class="item" :key="item" :data="item" @cut="onRemove(items, item)">{{item}}</drag>
                </template>
                <template v-slot:feedback="{data}">
                    <div class="item feedback" :key="data">{{data}}</div>
                </template>
            </drop-list>
        </div>

        <div class="col-md-3">
            <drop-list
                    :items="items2"
                    class="list"
                    @insert="onInsert2"
                    @reorder="$event.apply(items2)"
                    mode="cut"
            >
                <template v-slot:item="{item}">
                    <drag class="item" :key="item" :data="item" @cut="onRemove(items2, item)">{{item}}</drag>
                </template>
                <template v-slot:feedback="{data}">
                    <div class="item feedback" :key="data">{{data}}</div>
                </template>
            </drop-list>
        </div>

    </div>
</template>

<script>
import { Drag, DropList } from "vue-easy-dnd";

export default {
    name: "DDTest",
    components: {
        Drag,
        DropList
    },
    data: function() {
        return {
            nums: ['z', 'y', 'x', 'w', 'v'],
            items : ['a', 'b', 'c', 'd', 'e'],
            items2 : ['f', 'g', 'h', 'i', 'j']
        }
    },
    methods: {
        onInsert(event) {
            this.items.splice(event.index, 0, event.data);
        },
        onRemove(array, value) {
            let index = array.indexOf(value);
            array.splice(index, 1);
        },
        onInsert2(event) {
            this.items2.splice(event.index, 0, event.data);
        },
        remove(n) {
            let index = this.nums.indexOf(n);
            this.nums.splice(index, 1);
        }
    }
}
</script>

<style scoped>
.list {
    border: 1px solid black;
    margin: 100px auto;
    width: 200px;
}

.list .item {
    padding: 20px;
    margin: 10px;
    background-color: rgb(220, 220, 255);
    display: flex;
    align-items: center;
    justify-content: center;
}
.list .item.feedback {
    background-color: rgb(255, 220, 220);
    border: 2px dashed black;
}

.list .item.drag-image {
    background-color: rgb(220, 255, 220);
    transform: translate(-50%, -50%);
}
.drag-mode-reordering {
    border-left: 2px red solid;
}
</style>