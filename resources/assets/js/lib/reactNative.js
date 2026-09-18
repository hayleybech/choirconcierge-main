import { useEffect } from 'react';
import EventEmitter from 'events';

const RNEvents = new EventEmitter();

export const sendMessageToRN = message => {
	if (window.ReactNativeWebView) {
		window.ReactNativeWebView.postMessage(JSON.stringify(message));
	}
};

export const registerRNHandler = (action, callback) => {
	RNEvents.on(action, callback);

	return () => RNEvents.off(action, callback);
};

export const useRNHandler = (action, callback) => {
	useEffect(() => {
		const deregister = registerRNHandler(action, callback);

		return () => deregister();
	}, [action, callback]);
};

export const onMessageFromRN = message => {
	const { action, payload } = JSON.parse(message);

	RNEvents.emit(action, payload);
};

export const usePhoneBreadcrumb = (title, breadcrumbs) => {
	useEffect(() => {
			sendMessageToRN({
				action: 'setPageBreadcrumbs',
				payload: {
					title,
					breadcrumbs,
				}
			});
		}, []);
};
