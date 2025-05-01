const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/assets/js/app.js', 'public/js/app.js')
	.postCss('resources/assets/app.css', 'public/css/app.css', [
		require('tailwindcss')
	])
	.extract()
	.react()
	.ts('resources/assets/js/test.ts', 'public/js');

const path = require('path');
mix.alias({
	ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
});

mix.version();
