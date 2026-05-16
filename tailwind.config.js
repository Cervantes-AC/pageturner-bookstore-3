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
            animation: {
                'float': 'float 6s ease-in-out infinite',
                'float-slow': 'float 8s ease-in-out infinite',
                'pulse-soft': 'pulse-soft 3s ease-in-out infinite',
                'shimmer': 'shimmer 2s linear infinite',
                'spin-slow': 'spin 8s linear infinite',
                'bounce-gentle': 'bounce-gentle 2s ease-in-out infinite',
                'fade-in': 'fade-in 0.5s ease-out',
                'fade-in-up': 'fade-in-up 0.6s ease-out',
                'fade-in-down': 'fade-in-down 0.5s ease-out',
                'scale-in': 'scale-in 0.3s ease-out',
                'slide-in-right': 'slide-in-right 0.4s ease-out',
                'slide-in-left': 'slide-in-left 0.4s ease-out',
                'slide-up': 'slide-up 0.4s ease-out',
                'glow': 'glow 2s ease-in-out infinite alternate',
                'glow-gold': 'glow-gold 2s ease-in-out infinite alternate',
                'count-up': 'count-up 2s ease-out',
                'wiggle': 'wiggle 1s ease-in-out infinite',
                'typing': 'typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite',
                'blink': 'blink 1s step-end infinite',
                'ping-slow': 'ping-slow 2s cubic-bezier(0, 0, 0.2, 1) infinite',
                'heartbeat': 'heartbeat 1.5s ease-in-out infinite',
                'tilt': 'tilt 10s ease-in-out infinite',
                'glow-pulse': 'glow-pulse 2s ease-in-out infinite',
                'scroll-indicator': 'scroll-indicator 2s ease-in-out infinite',
                'page-load': 'page-load 0.8s ease-out',
                'reveal': 'reveal 0.8s ease-out forwards',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                'pulse-soft': {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.6' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'bounce-gentle': {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in-down': {
                    '0%': { opacity: '0', transform: 'translateY(-20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                'slide-in-right': {
                    '0%': { transform: 'translateX(100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                'slide-in-left': {
                    '0%': { transform: 'translateX(-100%)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                'slide-up': {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                glow: {
                    '0%': { boxShadow: '0 0 5px rgba(184, 134, 11, 0.2), 0 0 20px rgba(184, 134, 11, 0.1)' },
                    '100%': { boxShadow: '0 0 10px rgba(184, 134, 11, 0.4), 0 0 40px rgba(184, 134, 11, 0.2)' },
                },
                'glow-gold': {
                    '0%, 100%': { boxShadow: '0 0 8px rgba(184, 134, 11, 0.3), 0 0 16px rgba(184, 134, 11, 0.1)' },
                    '50%': { boxShadow: '0 0 16px rgba(184, 134, 11, 0.5), 0 0 40px rgba(184, 134, 11, 0.2)' },
                },
                wiggle: {
                    '0%, 100%': { transform: 'rotate(-3deg)' },
                    '50%': { transform: 'rotate(3deg)' },
                },
                typing: {
                    '0%': { width: '0' },
                    '100%': { width: '100%' },
                },
                blink: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0' },
                },
                'ping-slow': {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '75%, 100%': { transform: 'scale(2)', opacity: '0' },
                },
                heartbeat: {
                    '0%, 100%': { transform: 'scale(1)' },
                    '14%': { transform: 'scale(1.15)' },
                    '28%': { transform: 'scale(1)' },
                    '42%': { transform: 'scale(1.1)' },
                    '56%': { transform: 'scale(1)' },
                },
                tilt: {
                    '0%, 100%': { transform: 'rotate(-1deg)' },
                    '50%': { transform: 'rotate(1deg)' },
                },
                'glow-pulse': {
                    '0%, 100%': { opacity: '0.6' },
                    '50%': { opacity: '1' },
                },
                'scroll-indicator': {
                    '0%, 100%': { transform: 'translateY(0)', opacity: '1' },
                    '50%': { transform: 'translateY(8px)', opacity: '0.5' },
                },
                'page-load': {
                    '0%': { opacity: '0', transform: 'translateY(10px) scale(0.99)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
                reveal: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            },
        },
    },

    plugins: [forms],
};
