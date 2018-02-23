let mix = require('laravel-mix');


mix.typeScript('assets/js/app.tsx', 'public/assets/js')
   .sass('assets/sass/app.scss', 'public/assets/css')
   .version();

mix.browserSync({
    proxy: '192.168.99.100',
    files: [
        'assets/**/*.php',
        'public/**/*.css', 
        'public/**/*.js',    
    ]
});