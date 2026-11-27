import { vi } from 'vitest';

if (typeof globalThis.ResizeObserver === 'undefined') {
	globalThis.ResizeObserver = class {
		observe() {}

		unobserve() {}

		disconnect() {}
	};
}

vi.mock('@inertiajs/react', async () => {
	const { createElement, forwardRef } = await vi.importActual('react');

	return {
		usePage: vi.fn(() => ({ props: {} })),
		Link: forwardRef(({ children, ...props }, ref) => createElement('a', { ref, ...props }, children)),
	};
});
