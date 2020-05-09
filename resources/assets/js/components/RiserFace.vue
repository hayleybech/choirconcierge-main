<template>
    <div class="riser-face">
        <img :src="'https://api.adorable.io/avatars/50/'+ email + '.png'" :alt="singer_name">
        <div class="singer-name">{{singer_name}}</div>
    </div>
</template>

<script>
    export default {
        name: "RiserFace",
        props: {
            email: String,
            singer_name: String
        }
    }
</script>

<style scoped>

</style>