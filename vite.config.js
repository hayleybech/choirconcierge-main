import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
	server: {
		warmup: {
			clientFiles: [
				'resources/assets/js/app.js',
				// 'resources/assets/app.css',
				// 'resources/assets/js/Layouts/TenantLayout.jsx',
				// 'resources/assets/js/Pages/Dash/**/*.jsx',
			],
		},
	},
	plugins: [
		laravel({
			input: ['resources/assets/js/app.js'],
			refresh: true,
		}),
		react(),
		tailwindcss(),
	],
});
