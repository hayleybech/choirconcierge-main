import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import { transformWithEsbuild } from 'vite';

export default defineConfig({
	plugins: [
		{
			name: 'jsx-in-js-tests',
			enforce: 'pre',
			transform(code, id) {
				if (!id.endsWith('.test.js')) {
					return null;
				}

				return transformWithEsbuild(code, id, { loader: 'jsx', jsx: 'automatic' });
			},
		},
		react(),
	],
	test: {
		coverage: {
			enabled: true,
			include: ['resources/assets/js/**/*.{js,jsx}'],
			provider: 'v8',
			reportsDirectory: './coverage',
		},
		environment: 'jsdom',
		globals: true,
		include: ['resources/assets/js/**/*.test.js'],
		setupFiles: ['./setupVitestMock.js'],
	},
});
