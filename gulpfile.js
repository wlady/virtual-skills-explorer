var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.styles([
        'resources/assets/css/custom.css'
    ], 'public/css/custom.css');

    mix.scripts([
        'resources/assets/js/jquery.loadTemplate.min.js',
        'resources/assets/js/jquery.paging.min.js',
        'resources/assets/js/scripts.js'
    ], 'public/js/scripts.js');

    mix.version([
        'public/css/custom.css',
        'public/js/scripts.js'
    ]);

});
