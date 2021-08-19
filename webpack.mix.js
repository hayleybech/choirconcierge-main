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
const rebuild = process.env.MIX_FEATURE_REBUILD;

console.log('Mixing both React and Vue');

mix.js('resources/assets/js/app-rebuild.js', 'public/js/app-rebuild.js')
	// .postCss('resources/assets/app.css', 'public/css/app-rebuild.css', [
	// 	require('tailwindcss')
	// ])
	.react()

	.js('resources/assets/js/app.js', 'public/js')
	.ts('resources/assets/js/test.ts', 'public/js')
	.sass('resources/assets/sass/app.scss', 'public/css')
	.vue();

const path = require('path');
mix.alias({
	ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
});

// if(rebuild === 'true') {
// 	console.log('Mixing React');
//
// 	mix.js('resources/assets/js/app-rebuild.js', 'public/js/app-rebuild.js')
// 		.postCss('resources/assets/app.css', 'public/css/app-rebuild.css', [
// 			require('tailwindcss')
// 		])
// 		.react();
//
// 	const path = require('path');
// 	mix.alias({
// 		ziggy: path.resolve('vendor/tightenco/ziggy/dist'),
// 	});
//
// } else {
// 	console.log('Mixing Vue');
//
// 	mix.js('resources/assets/js/app.js', 'public/js')
// 		.ts('resources/assets/js/test.ts', 'public/js')
// 		.sass('resources/assets/sass/app.scss', 'public/css')
// 		.vue();
// }

if (mix.inProduction()) {
	mix.version();
}
