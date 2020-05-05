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

mix.js('app/Modules/js/index.js', 'public/js/app.js')
    .sass('app/Modules/sass/index.scss', 'public/css/app.css')

    .js('app/Modules/Admin/js/index.js', 'public/js/append.js')
    .sass('app/Modules/Admin/sass/index.scss', 'public/css/append.css')
    .js('app/Modules/Shop/js/index.js', 'public/js/shop.js')
    .sass('app/Modules/Shop/sass/index.scss', 'public/css/shop.css')
    .js('app/Modules/Auth/js/index.js', 'public/js/auth.js')
    .sass('app/Modules/Auth/sass/index.scss', 'public/css/auth.css');

