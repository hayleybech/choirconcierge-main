/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue';

const VERSION = 'choir-concierge@2021-08-04a';

require('./bootstrap');
require('select2');

import ReadMore from 'vue-read-more';
Vue.use(ReadMore);

import * as Sentry from '@sentry/vue';
import { Integrations as TracingIntegrations } from '@sentry/tracing';
Sentry.init({
	Vue: Vue,
	dsn: process.env.MIX_SENTRY_DSN,
	logErrors: true,
	integrations: [new TracingIntegrations.BrowserTracing()],
	tracesSampleRate: process.env.MIX_SENTRY_TRACES_SAMPLE_RATE,
	tracingOptions: {
		trackComponents: true,
	},
	release: process.env.MIX_APP_ENV === 'production' ? VERSION : VERSION + ':dev',
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key =>
	Vue.component(
		key
			.split('/')
			.pop()
			.split('.')[0],
		files(key).default
	)
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Allow binding select2 fields (add v-select attribute to select element to enable)
// https://stackoverflow.com/a/51260727/563974
Vue.directive('select2', {
	inserted(el) {
		$(el).on('select2:select', () => {
			const event = new Event('change', { bubbles: true, cancelable: true });
			el.dispatchEvent(event);
		});

		$(el).on('select2:unselect', () => {
			const event = new Event('change', { bubbles: true, cancelable: true });
			el.dispatchEvent(event);
		});
	},
});

const app = new Vue({
	el: '#app',
	data() {
		return {
			loading: true,
		};
	},
	created() {
		this.loading = false;
	},
});

//const helloWorld = require('./test').test();
//console.log(helloWorld);
