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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            /* ✅ ZANIS TV BRAND COLORS */
            colors: {
                zanis: {
                    green: '#005000',
                    green2: '#107000',
                    orange: '#F08000',
                    orange2: '#D04000',
                    black: '#000000',
                },
            },
        },
    },

    plugins: [forms],
};
