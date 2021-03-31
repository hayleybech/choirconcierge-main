<template>
	<div>
		<label :for="inputName">{{ label }}</label>
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa fa-fw fa-map-marker-alt"></i></span>
			</div>

			<input ref="locationInput" type="text" :name="inputName" :id="inputName" :value="value" @change="clear" class="form-control">
		</div>

		<input type="hidden" :name="inputName+'_place_id'" :value="location.placeId">
		<input type="hidden" :name="inputName+'_icon'" :value="location.icon">
		<input type="hidden" :name="inputName+'_name'" :value="location.name">
		<input type="hidden" :name="inputName+'_address'" :value="location.address">

		<small class="location-place form-text text-muted">
			<span class="place-icon" :style="iconStyle"></span>
			<span class="place-name">{{ location.name }}</span>
			<span class="place-address">{{ location.address }}</span>
		</small>
	</div>
</template>

<script>
import { Loader } from '@googlemaps/js-api-loader';
export default {
	name: "LocationInput",
	props: {
		inputName: {
			type: String,
			required: true,
		},
		label: {
			type: String,
			default: '',
		},
		locationName: {
			type: String,
		},
		locationAddress: {
			type: String,
		},
		locationPlaceId: {
			type: String,
		},
		locationIcon: {
			type: String,
		},
		apiKey: {
			type: String,
			required: true,
		}
	},
	data() {
		return {
			loader: null,
			autocomplete: null,
			location: {
				name: this.locationName,
				address: this.locationAddress,
				placeId: this.locationPlaceId,
				icon: this.locationIcon
			}
		}
	},
	computed: {
		value() {
			if(! this.location.name || ! this.location.address) {
				return '';
			}
			return this.location.name+', '+this.location.address;
		},
		iconStyle() {
			if(! this.location.icon) {
				return '';
			}
			return 'background-image: url("'+this.location.icon+'");'
		}
	},
	async mounted() {
		this.loader = new Loader({
			apiKey: this.apiKey,
			version: "weekly",
			libraries: ["places"]
		});

		await this.loader.load();
		this.autocomplete = new google.maps.places.Autocomplete(this.$refs.locationInput);


		// Set the data fields to return when the user selects a place.
		this.autocomplete.setFields(['place_id', 'address_components', 'icon', 'name']);

		this.autocomplete.addListener('place_changed', this.placeChanged);
	},
	methods: {
		placeChanged() {
			let place = this.autocomplete.getPlace();

			if ( ! place.place_id ) {
				// User entered the name of a Place that was not suggested and
				// pressed the Enter key, or the Place Details request failed.
				window.alert("No details available for input: '" + place.name + "'");
				return;
			}

			// Format the address
			let address = '';
			let address_parts = [];
			if (place.address_components) {
				place.address_components.forEach(function(part){
					if(part.short_name) {
						address_parts.push(part.short_name);
					}
				});

				address = address_parts.join(', ');
			}

			// Save the place details
			this.location.placeId = place.place_id;
			this.location.icon = place.icon;
			this.location.name = place.name;
			this.location.address = address;
		},
		clear() {
			// Clear the location when the input is cleared
			this.location.placeId = '';
			this.location.icon = '';
			this.location.name = '';
			this.location.address = '';
		},
	}
}
</script>

<style scoped>

</style>