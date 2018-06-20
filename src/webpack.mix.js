let mix = require('laravel-mix');


mix.typeScript('front/assets/js/app.tsx', 'public/assets/js')
   .sass('front/assets/sass/app.scss', 'public/assets/css');

if(mix.config.production) {
    mix.extract([
            'react', 
            'moment', 
            'react-dom', 
            'axios'
        ])
        .version();

} else {
    mix.browserSync({
        proxy: '192.168.99.100',
        files: [
            'front/**/*.php',
            'public/**/*.css', 
            'public/**/*.js',    
        ]
    });
}