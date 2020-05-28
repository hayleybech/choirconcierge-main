<template>
    <div class="riser-face">
        <img :src="'https://api.adorable.io/avatars/50/'+ singer.email + '.png'" :alt="singer.name">
        <div class="singer-name">{{singer.name}}</div>
    </div>
</template>

<script>
export default {
    name: "RiserFace",
    props: {
        singer: {
            id: 0,
            name: '',
            email: '',
            part: ''
        }
    }
}
</script>

<style scoped>

</style>