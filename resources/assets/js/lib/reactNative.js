import { useEffect } from 'react';

const RNEvents = new Map();

export const sendMessageToRN = message => {
	if (window.ReactNativeWebView) {
		window.ReactNativeWebView.postMessage(JSON.stringify(message));
	}
};

export const registerRNHandler = (action, callback) => {
	const handlers = RNEvents.get(action) || [];
	handlers.push(callback);
	RNEvents.set(action, handlers);

	return () => {
		const handlerIndex = handlers.lastIndexOf(callback);

		if (handlerIndex !== -1) {
			handlers.splice(handlerIndex, 1);
		}

		if (handlers.length === 0 && RNEvents.get(action) === handlers) {
			RNEvents.delete(action);
		}
	};
};

export const useRNHandler = (action, callback) => {
	useEffect(() => {
		const deregister = registerRNHandler(action, callback);

		return () => deregister();
	}, [action, callback]);
};

export const onMessageFromRN = message => {
	const { action, payload } = JSON.parse(message);

	RNEvents.get(action)?.slice().forEach(callback => callback(payload));
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
