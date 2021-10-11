const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    mode: 'jit',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
        textColor: theme => theme('colors'),
        textColor: {
        'ttds': '#4A98AC',
        },
        backgroundColor: theme => theme('colors'),
        backgroundColor: {
        'ttds-old': '#0F84B7',
        'ttds':'#186D92',
        'ttds-encabezado-old':'#3498DB',
        'ttds-encabezado':'#186D92',
        'ttds-secundario-old':'#E9FEFB',
        'ttds-secundario':'#E6F9FF',
        'ttds-secundario-2':'#e9f6fb',
        'ttds-hover-old': '#247286',
        'ttds-hover': '#12526E',
        },
        gradientColorStops: theme => theme('colors'),
        gradientColorStops: {
        'ttdsfrom':'#0F84B7',
        'ttdsto':'#25D4B9',
        'ttds-1':'#293D3D'
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
