import React from 'react'
import { render } from 'react-dom'
import { createInertiaApp } from '@inertiajs/inertia-react'
import { InertiaProgress } from '@inertiajs/progress'

// @todo install sentry react
const VERSION = 'choir-concierge@2021-08-10a';

createInertiaApp({
	resolve: name => require(`./Pages/${name}`),
	setup({ el, App, props }) {
		render(<App {...props} />, el)
	},
});

InertiaProgress.init();
