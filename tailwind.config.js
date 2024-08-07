import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Advent Pro', ...defaultTheme.fontFamily.sans],
                serif: ['artifex-cf', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],

    safelist: [
        'grid',
        'grid-cols-5',
        'space-y-4',
        'space-x-4',
        'items-center',
        'border-0',
        '!border-0',
        {
            pattern: /col-span-\d+/
        },
        'text-right',
        {
            pattern: /text-([2-9xsml]{2,3}|base)/,
            variants: ['md']
        },
        {
            pattern: /mt-\d/
        }
    ]
};
