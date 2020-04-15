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

//mix.js('resources/js/app.js', 'public/js').sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/admin/index.js', 'public/js/append.js')
    .sass('resources/sass/app/index.scss', 'public/css/app.css')
    .sass('resources/sass/admin/index.scss', 'public/css/append.css');
