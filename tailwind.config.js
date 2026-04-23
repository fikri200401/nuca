import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    light: '#ff8fa3',
                    DEFAULT: '#ff4d88',
                    dark: '#e03e73',
                },
            },
            fontFamily: {
                sans: ['system-ui', ...defaultTheme.fontFamily.sans],
                serif: ['Georgia', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
