import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                kosgoro: {
                    navy: '#0B3D91',
                    'navy-dark': '#082C6B',
                    'navy-light': '#EFF4FC',
                    gold: '#CA8A04',
                    'gold-light': '#FEF3C7',
                },
            },
            fontFamily: {
                display: ['Lora', 'serif'],
                sans: ['Inter', 'sans-serif'],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
