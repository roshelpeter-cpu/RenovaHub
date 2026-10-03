import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                outfit: ['Outfit', ...defaultTheme.fontFamily.sans],
                serif: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                forest: '#173F2A',
                leaf: '#2F6B49',
                olive: '#71856B',
                cream: '#F7F4EC',
                sand: '#EAE4D8',
                ivory: '#FCFBF8',
                charcoal: '#1E2420',
                mist: '#66706A',
                line: '#DDD8CD',
            },
        },
    },

    plugins: [forms, typography],
};
