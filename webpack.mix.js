mix = require('laravel-mix');

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

mix.js([
    'resources/assets/js/app.js',
    ], 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')

    //引入新的主题：
    .styles([
        //old css
        'resources/assets/css/font-awesome.css',
        'resources/assets/css/bootstrap-treeview.css',
        'resources/assets/css/fileinput/fileinput.css',
        'resources/assets/css/fileinput/fileinput-rtl.css',
        'resources/assets/css/tagging.css',
        'resources/assets/css/fix.css',

        //新的主题 css
        'public/static/css/vendor/bootstrap.css',
        'public/static/vendor/metismenu/dist/metisMenu.css',
        'public/static/vendor/switchery-npm/index.css',
        'public/static/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
        'public/static/css/icons/line-awesome.min.css',
        'public/static/css/icons/dripicons.min.css',
        'public/static/css/icons/material-design-iconic-font.min.css',
        'public/static/css/common/main.bundle.css',
        'public/static/css/layouts/vertical/core/main.css',
        'public/static/css/layouts/vertical/menu-type/default.css',
        'public/static/css/layouts/vertical/themes/theme-a.css',
        'public/static/css/min/toastr.min.css',
        //时间控件样式
        'public/static/vender/bootstrap-datepicker/bootstrap-datepicker.min.css',
        'public/static/vender/boostrap-daterangepicker/bootstrapdaterangepicker.min.css',

        'public/css/select2/select2.min.css'   //select2输入框搜索css
    ], 'public/static/css/min/screen.css')
    .copy('resources/assets/js/bootstrap-treeview.js', 'public/js/bootstrap-treeview.js');



if (mix.inProduction()) {
    mix.version();
}