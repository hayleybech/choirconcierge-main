import React from 'react'
import { render } from 'react-dom'
import { createInertiaApp } from '@inertiajs/inertia-react'
import { InertiaProgress } from '@inertiajs/progress'
import * as Sentry from '@sentry/react';
import {Integrations as TracingIntegrations} from "@sentry/tracing";

const VERSION = 'choir-concierge@2022-02-22a';

Sentry.init({
	dsn: process.env.MIX_SENTRY_DSN,
	logErrors: true,
	integrations: [new TracingIntegrations.BrowserTracing()],
	tracesSampleRate: process.env.MIX_SENTRY_TRACES_SAMPLE_RATE,
	tracingOptions: {
		trackComponents: true,
	},
	release: process.env.MIX_APP_ENV === 'production' ? VERSION : VERSION + ':dev',
})

createInertiaApp({
	resolve: name => require(`./Pages/${name}`),
	setup({ el, App, props }) {
		render(<App {...props} />, el)
	},
});

InertiaProgress.init();
