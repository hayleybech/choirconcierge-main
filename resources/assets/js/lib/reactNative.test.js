import { onMessageFromRN, registerRNHandler, sendMessageToRN } from './reactNative';
import { vi } from 'vitest';

describe('React Native bridge', () => {
	it('sends messages to the React Native WebView', () => {
		window.ReactNativeWebView = { postMessage: vi.fn() };

		sendMessageToRN({ action: 'start', payload: '/dashboard' });

		expect(window.ReactNativeWebView.postMessage).toHaveBeenCalledWith(
			JSON.stringify({ action: 'start', payload: '/dashboard' })
		);
	});

	it('dispatches messages to registered handlers', () => {
		const callback = vi.fn();
		const deregister = registerRNHandler('navigation', callback);

		onMessageFromRN(JSON.stringify({ action: 'navigation', payload: { url: '/dashboard' } }));

		expect(callback).toHaveBeenCalledWith({ url: '/dashboard' });

		deregister();
	});

	it('loads without a Node event emitter', async () => {
		vi.resetModules();
		vi.doMock('events', () => ({}));

		try {
			await expect(import('./reactNative')).resolves.toBeDefined();
		} finally {
			vi.doUnmock('events');
			vi.resetModules();
		}
	});
});
