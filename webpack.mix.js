let mix = require("laravel-mix");

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

mix.js("resources/assets/js/app.js", "public/js/app.min.js")
   .sass("resources/assets/sass/app.scss", "public/css/app.min.css");

// My mixes
mix.sass("resources/assets/sass/fixes.scss", "public/css/fixes.min.css");
mix.js("resources/assets/js/deletemodal.js", "public/js/deletemodal.min.js");
mix.js("resources/assets/js/datepickersettings.js", "public/js/datepickersettings.min.js");

// Cache busting
mix.version();
