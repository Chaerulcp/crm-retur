import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // Kelas Tailwind dinamis (mis. badgeClass pada enum) berada di kode PHP.
        './app/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6', // Teal
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                ai: {
                    light: '#e0e7ff',
                    DEFAULT: '#6366f1', // Indigo
                    dark: '#4338ca',
                    glow: '#818cf8',
                },
                ink: '#0f172a',
                paper: '#f8fafc',
            },
            fontFamily: {
                display: ['"Bricolage Grotesque"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                sans: ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"Fira Code"', ...defaultTheme.fontFamily.mono],
            },
            animation: {
                'blob': 'blob 7s infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                blob: {
                    '0%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '100%': { transform: 'translate(0px, 0px) scale(1)' },
                }
            }
        },
    },

    plugins: [forms],
};
