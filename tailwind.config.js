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
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // BHKnow brand palette — drawn directly from the logo's deep
                // navy wordmark and warm gold roofline, not a generic
                // Tailwind scheme. primary = navy (chrome, type, CTAs on
                // light surfaces), secondary = gold (accents, highlights).
                primary: {
                    50: '#eef3f8',
                    100: '#d9e3ee',
                    200: '#b3c7dd',
                    300: '#88a6c7',
                    400: '#5c7fa6',
                    500: '#3c5c80',
                    600: '#28415f',
                    700: '#1b3a5c',
                    800: '#152d47',
                    900: '#0f2034',
                },
                secondary: {
                    50: '#fdf6e9',
                    100: '#faebc9',
                    200: '#f5d68e',
                    300: '#efc164',
                    400: '#e8b34e',
                    500: '#e2a63d',
                    600: '#c98f2e',
                    700: '#a67324',
                    800: '#7d571b',
                    900: '#543a12',
                },
                neutral: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                },
            },
        },
    },

    plugins: [forms],
};
