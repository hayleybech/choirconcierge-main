
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'

require('./bootstrap');
require('select2');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('riser-face', require('./components/RiserFace.vue').default);
Vue.component('riser-stack', require('./components/RiserStack.vue').default);
Vue.component('holding-area', require('./components/HoldingArea.vue').default);
Vue.component('riser-spot', require('./components/RiserSpot').default);
Vue.component('load-button', require('./components/LoadButton').default);
Vue.component('track-list', require('./components/TrackList').default);
Vue.component('track-player', require('./components/TrackPlayer').default);
Vue.component('track-list-player', require('./components/TrackListPlayer').default);
Vue.component('pitch-button', require('./components/PitchButton').default);

const app = new Vue({
    el: '#app'
});


//const helloWorld = require('./test').test();
//console.log(helloWorld);

