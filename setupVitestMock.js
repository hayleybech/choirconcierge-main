import { vi } from 'vitest';

vi.mock('@inertiajs/react', async () => {
	const { createElement, forwardRef } = await vi.importActual('react');

	return {
		usePage: vi.fn(() => ({ props: {} })),
		Link: forwardRef(({ children, ...props }, ref) => createElement('a', { ref, ...props }, children)),
	};
});
