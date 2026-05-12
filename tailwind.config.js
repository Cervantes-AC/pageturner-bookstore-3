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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                heading: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                ink: {
                    50: '#f0f0f5',
                    100: '#d6d6e0',
                    200: '#b3b3c2',
                    300: '#8a8a9e',
                    400: '#6b6b80',
                    500: '#4a4a5a',
                    600: '#2d2d3a',
                    700: '#1e1e2d',
                    800: '#151524',
                    900: '#0d0d1a',
                    950: '#080812',
                },
                gold: {
                    50: '#fdf8ed',
                    100: '#f9edcc',
                    200: '#f3d994',
                    300: '#ecc05c',
                    400: '#e7ab36',
                    500: '#d48f1f',
                    600: '#b8860b',
                    700: '#8e6a0e',
                    800: '#6e5313',
                    900: '#5a4414',
                    950: '#34250a',
                },
                parchment: {
                    50: '#fdfcf9',
                    100: '#faf6f0',
                    200: '#f2eadc',
                    300: '#e8dac4',
                    400: '#dcc5a4',
                    500: '#d0ae84',
                    600: '#c49a6a',
                    700: '#a88255',
                    800: '#8a6949',
                    900: '#71563e',
                    950: '#3d2e20',
                },
            },
        },
    },

    plugins: [forms],
};
