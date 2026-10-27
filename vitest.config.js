import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';

export default defineConfig({
	plugins: [react()],
	test: {
		coverage: {
			enabled: true,
			include: ['resources/assets/js/**/*.{js,jsx}'],
			provider: 'v8',
			reportsDirectory: './coverage',
		},
		environment: 'jsdom',
		globals: true,
		include: ['resources/assets/js/**/*.test.{js,jsx}'],
		setupFiles: ['./setupVitestMock.js'],
	},
});
