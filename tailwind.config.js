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
        'ttds': '#0F84B7',
        'ttds-encabezado':'#3498DB',
        'ttds-secundario':'#E9FEFB',
        'ttds-hover': '#247286',
        },
        gradientColorStops: theme => theme('colors'),
        gradientColorStops: {
        'ttdsfrom':'#0F84B7',
        'ttdsto':'#25D4B9',
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
