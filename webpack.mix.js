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
mix.copy('resources/js/custom.js', 'public/js/custom.js');
mix.copy('resources/images', 'public/images');
mix.copy('resources/*.ico', 'public/');
mix.sass('resources/scss/app.scss', 'public/css');
mix.copy('node_modules/@fortawesome/fontawesome-free', 'public/fontawesome-free');
mix.copy('resources/file', 'public/file');

mix.js('resources/js/app.js', 'public/js').extract([
    'lodash', 'popper.js', 'jquery', 'bootstrap',
    'dropzone', 'icheck', 'select2', 'eonasdan-bootstrap-datetimepicker',
    'summernote', 'bootstrap-datepicker',
    'datatables.net-bs4', 'datatables.net-buttons-bs4', 'datatables.net-fixedcolumns-bs4',
    'datatables.net-fixedheader-bs4', 'datatables.net-keytable-bs4', 'datatables.net-responsive-bs4',
    'datatables.net-rowgroup-bs4', 'datatables.net-select-bs4',
    'datatables.net-buttons/js/buttons.colVis',
    'datatables.net-buttons/js/buttons.html5',
    'datatables.net-buttons/js/buttons.print',
]);

mix.autoload({
    'lodash': ['_'],
    'jquery': ['$', 'window.jQuery', "jQuery"],
    'popper.js/dist/umd/popper.js': ['Popper', 'window.Popper']
});

mix.version();
mix.setPublicPath('public');