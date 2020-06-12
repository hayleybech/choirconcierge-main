<template>
    <div class="riser-face">
        <img :src="singer.user_avatar_thumb_url" :alt="singer.name">
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
            part: '',
            user_avatar_thumb_url: ''
        }
    }
}
</script>

<style scoped>
.riser-face {
    display: flex;
    align-items: center;
}
.riser-face img {
    height: 1.5em;
    margin-right: 10px;
}
</style>