import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // queuing.js builds the display board and staff "up next" list as
        // HTML strings — without this, those class names only survive a
        // production build by accident, if the same string also happens
        // to appear in a scanned .blade.php file.
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // Ticket numbers, "now serving" digits, and timestamps only —
                // tabular figures and unambiguous digit shapes for anything
                // read fast or from across a room. Never used for prose.
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
            // Single locked brand accent (teal) used for nav, links, and primary
            // actions across the app. Deliberately distinct from the yellow /
            // blue / green queue-status badges so it never reads as a status.
            colors: {
                brand: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
            },
            keyframes: {
                // Entrance for cards/tickets. Transform + opacity only (GPU-cheap).
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                // One-shot emphasis when a served number changes on the public display.
                'flash-once': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '60%': { opacity: '1', transform: 'scale(1.02)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) both',
                'flash-once': 'flash-once 0.5s cubic-bezier(0.16, 1, 0.3, 1) both',
            },
        },
    },

    plugins: [forms],
};
