import React from 'react';
import { render } from 'react-dom';
import { createInertiaApp, router } from '@inertiajs/react';
import * as Sentry from '@sentry/react';
import { Integrations as TracingIntegrations } from '@sentry/tracing';
import { onMessageFromRN, sendMessageToRN } from './lib/reactNative';

const VERSION = 'choir-concierge@2025-09-22c';

Sentry.init({
	dsn: process.env.MIX_SENTRY_DSN,
	logErrors: true,
	integrations: [new TracingIntegrations.BrowserTracing()],
	tracesSampleRate: process.env.MIX_SENTRY_TRACES_SAMPLE_RATE,
	tracingOptions: {
		trackComponents: true,
	},
	release: process.env.MIX_SENTRY_ENV === 'production' ? VERSION : `VERSION:${process.env.MIX_SENTRY_ENV}`,
	environment: process.env.MIX_SENTRY_ENV,
});
createInertiaApp({
	resolve: name => require(`./Pages/${name}`),
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
