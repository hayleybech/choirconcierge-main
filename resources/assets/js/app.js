import React from 'react'
import { render } from 'react-dom'
import { createInertiaApp } from '@inertiajs/react'
import * as Sentry from '@sentry/react';
import {Integrations as TracingIntegrations} from "@sentry/tracing";

const VERSION = 'choir-concierge@2025-01-20a';

Sentry.init({
	dsn: process.env.MIX_SENTRY_DSN,
	logErrors: true,
	integrations: [new TracingIntegrations.BrowserTracing()],
	tracesSampleRate: process.env.MIX_SENTRY_TRACES_SAMPLE_RATE,
	tracingOptions: {
		trackComponents: true,
	},
	release: process.env.MIX_APP_ENV === 'production' ? VERSION : VERSION + ':dev',
	environment: process.env.MIX_APP_ENV,
});

createInertiaApp({
	resolve: name => require(`./Pages/${name}`),
	setup({ el, App, props }) {
		render(<App {...props} />, el)
	},
	progress: { color: '#38bdf8' },
});
