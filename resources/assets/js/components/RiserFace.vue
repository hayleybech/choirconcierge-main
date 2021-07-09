<template>
	<div class="riser-face">
		<img :src="singer.user_avatar_thumb_url" :alt="singer.user.name" />
		<div class="singer-name">{{ singer.user.name }}</div>
	</div>
</template>

<script>
export default {
	name: 'RiserFace',
	props: {
		singer: {
			id: 0,
			part: '',
      user_avatar_thumb_url: '',
      user: {
        name: '',
        email: '',
      }
    },
	},
};
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
