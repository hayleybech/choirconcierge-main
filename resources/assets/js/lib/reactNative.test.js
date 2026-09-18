import { onMessageFromRN, registerRNHandler, sendMessageToRN } from './reactNative';

describe('React Native bridge', () => {
	it('sends messages to the React Native WebView', () => {
		window.ReactNativeWebView = { postMessage: jest.fn() };

		sendMessageToRN({ action: 'start', payload: '/dashboard' });

		expect(window.ReactNativeWebView.postMessage).toHaveBeenCalledWith(
			JSON.stringify({ action: 'start', payload: '/dashboard' })
		);
	});

	it('dispatches messages to registered handlers', () => {
		const callback = jest.fn();
		const deregister = registerRNHandler('navigation', callback);

		onMessageFromRN(JSON.stringify({ action: 'navigation', payload: { url: '/dashboard' } }));

		expect(callback).toHaveBeenCalledWith({ url: '/dashboard' });

		deregister();
	});
});
