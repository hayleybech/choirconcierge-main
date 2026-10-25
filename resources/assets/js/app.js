import React from 'react';
import { render } from 'react-dom';
import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import * as Sentry from '@sentry/react';
import { Integrations as TracingIntegrations } from '@sentry/tracing';
import { onMessageFromRN, sendMessageToRN } from './lib/reactNative';

import 'react-pdf/dist/Page/AnnotationLayer.css';
import '../app.css';

const VERSION = 'choir-concierge@2025-09-22c';
const pages = import.meta.glob(['./Pages/**/*.js', './Pages/**/*.jsx', '!./Pages/**/*.test.js']);

Sentry.init({
	dsn: import.meta.env.VITE_SENTRY_DSN,
	logErrors: true,
	integrations: [new TracingIntegrations.BrowserTracing()],
	tracesSampleRate: import.meta.env.VITE_SENTRY_TRACES_SAMPLE_RATE,
	tracingOptions: {
		trackComponents: true,
	},
	release: import.meta.env.VITE_SENTRY_ENV === 'production' ? VERSION : `VERSION:${import.meta.env.VITE_SENTRY_ENV}`,
	environment: import.meta.env.VITE_SENTRY_ENV,
});
createInertiaApp({
	resolve: name =>
		resolvePageComponent([`./Pages/${name}.js`, `./Pages/${name}.jsx`], pages).then(module => module.default),
	setup({ el, App, props }) {
		if (props.initialPage.props.isWebView) {
			router.on('before', event => {
				event.detail.visit.headers['X-WebView-Source'] = 'react-native-app';
			});
			router.on('start', event =>
				sendMessageToRN({
					action: 'start',
					payload: event.detail.visit.url,
				})
			);
		}

		render(<App {...props} />, el);
	},
	progress: { color: '#38bdf8' },
});

window.onMessageFromRN = onMessageFromRN;
