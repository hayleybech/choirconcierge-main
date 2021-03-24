
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
Vue.component('inline-edit-field', require('./components/InlineEditField').default);
Vue.component('limited-textarea', require('./components/LimitedTextarea').default);
Vue.component('impersonate-user-modal', require('./components/ImpersonateUserModal').default);
Vue.component('repeating-event-edit-mode-modal', require('./components/RepeatingEventEditModeModal').default);
Vue.component('repeating-event-delete-modal', require('./components/RepeatingEventDeleteModal').default);
Vue.component('repeating-event-delete-button', require('./components/RepeatingEventDeleteButton').default);
Vue.component('input-datetime-range', require('./components/inputs/DateTimeRange').default);
Vue.component('input-datetime', require('./components/inputs/DateTime').default);

// Allow binding select2 fields (add v-select attribute to select element to enable)
// https://stackoverflow.com/a/51260727/563974
Vue.directive('select2', {
    inserted(el) {
        $(el).on('select2:select', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event);
        });

        $(el).on('select2:unselect', () => {
            const event = new Event('change', {bubbles: true, cancelable: true})
            el.dispatchEvent(event)
        })
    },
});

const app = new Vue({
    el: '#app'
});


//const helloWorld = require('./test').test();
//console.log(helloWorld);

