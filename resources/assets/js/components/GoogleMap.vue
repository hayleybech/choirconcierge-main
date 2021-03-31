<template>
	<div class="google-map">
		<iframe v-if="placeId"
			width="600"
			height="450"
			frameborder="0" style="border:0"
			:src="'https://www.google.com/maps/embed/v1/place?key='+apiKey+'&q=place_id:'+placeId" allowfullscreen>
		</iframe>
	</div>
</template>

<script>
export default {
	name: "GoogleMap",
	props: {
		apiKey: {
			type: String,
			required: true,
		},
		placeId: {
			type: String,
		}
	}
}
</script>

<style scoped>
/** RESPONSIVE GOOGLE MAPS EMBED **/
.google-map {
    position: relative;
    padding-bottom: 56.25%; /* This is the aspect ratio */
    height: 0;
    overflow: hidden;
}
.google-map iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
}
</style>