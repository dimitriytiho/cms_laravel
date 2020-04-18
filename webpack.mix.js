const mix = require('laravel-mix');


mix.js('app/Modules/publicly/js/index.js', 'public/js/app.js')
.sass('app/Modules/publicly/sass/index.scss', 'public/css/app.css')
.js('app/Modules/publicly/Shop/js/index.js', 'public/js/shop.js')
.sass('app/Modules/publicly/Shop/sass/index.scss', 'public/css/shop.css')
.js('app/Modules/publicly/Page/js/index.js', 'public/js/page.js')
.sass('app/Modules/publicly/Page/sass/index.scss', 'public/css/page.css')
.js('resources/js/admin/index.js', 'public/js/append.js')
.sass('resources/sass/admin/index.scss', 'public/css/append.css');
