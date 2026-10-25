import { defineConfig, transformWithEsbuild } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
	server: {
		warmup: {
			clientFiles: [
				'resources/assets/js/app.js',
				// 'resources/assets/app.css',
				// 'resources/assets/js/Layouts/TenantLayout.js',
				// 'resources/assets/js/Pages/Dash/**/*.js',
			],
		},
	},
	optimizeDeps: {
		esbuildOptions: {
			loader: { '.js': 'jsx' },
		},
	},
	plugins: [
		{
			name: 'transform-jsx-in-js',
			enforce: 'pre',
			transform(code, id) {
				if (!id.replaceAll('\\', '/').includes('/resources/assets/js/') || !id.endsWith('.js')) {
					return null;
				}

				return transformWithEsbuild(code, id, { loader: 'jsx', jsx: 'automatic' });
			},
		},
		laravel({
			input: ['resources/assets/js/app.js'],
			refresh: true,
		}),
		react(),
		tailwindcss(),
	],
});
