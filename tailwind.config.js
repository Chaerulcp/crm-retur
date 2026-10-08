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
                // Palet "Kantor Operasional": petrol-teal dalam untuk aksi utama & sidebar.
                brand: {
                    50: '#EFF6F6',
                    100: '#DCEBEA',
                    200: '#BAD7D6',
                    300: '#8FBCBC',
                    400: '#5C9A9C',
                    500: '#3B7E82',
                    600: '#22646A',
                    700: '#1A5058',
                    800: '#154049',
                    900: '#10333C',
                },
                ink: '#1C2730',
                paper: '#F4F6F6',
            },
            fontFamily: {
                display: ['"Bricolage Grotesque"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
        },
    },

    plugins: [forms],
};
