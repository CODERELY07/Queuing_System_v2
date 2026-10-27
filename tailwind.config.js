import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // `dark:` still follows the OS (as before), except inside anything
    // marked `.light`. The public pages (home, kiosk, login, display) put
    // that class on <html> so they're always light — a white header/footer
    // and a paper-colored page — while the staff/admin console keeps
    // following the visitor's theme.
    darkMode: ['variant', '@media (prefers-color-scheme: dark) { &:not(.light, .light *) }'],

    content: [
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
                sans: ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
                // Headlines on the public pages only (home hero, kiosk,
                // ticket) — a bookish serif against the plain sans body
                // text is what keeps those pages from reading as a SaaS
                // template. Never used inside the staff/admin console.
                display: ['Newsreader', 'Georgia', ...defaultTheme.fontFamily.serif],
                // Ticket numbers, "now serving" digits, and timestamps only —
                // tabular figures and unambiguous digit shapes for anything
                // read fast or from across a room. Never used for prose.
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },

            colors: {
                // Warm neutral ramp replacing Tailwind's blue-tinted gray.
                // Every existing `gray-*` utility in the views picks this up
                // automatically, so the whole app shifts from cool "SaaS"
                // gray to a paper-and-ink palette without per-file edits.
                // In dark mode it reads as warm charcoal rather than the
                // navy-black Tailwind's default gray produces.
                gray: {
                    50: '#f8f7f4',
                    100: '#f0eee9',
                    200: '#e5e2db',
                    300: '#d1cdc3',
                    400: '#a29d91',
                    500: '#6f6a62',
                    600: '#57534c',
                    700: '#3f3c37',
                    800: '#2a2825',
                    900: '#1c1b19',
                    950: '#121110',
                },
                // The single accent: a deep, desaturated petrol. Used for
                // primary actions, links, focus, and the active nav item —
                // deliberately quiet, and distinct from the amber / blue /
                // green / rose / violet that only ever mean queue status.
                brand: {
                    50: '#eef5f5',
                    100: '#d6e8e8',
                    200: '#aed1d2',
                    300: '#7db0b3',
                    400: '#4d8b90',
                    500: '#2f6f76',
                    600: '#245a60',
                    700: '#1d484d',
                    800: '#183a3e',
                    900: '#142f32',
                    950: '#0b1c1e',
                },
            },

            // Tighter, more deliberate corners than Tailwind's defaults:
            // 8px controls, 14px surfaces. Remapping the existing scale
            // (rather than editing every `rounded-*` in the views) keeps
            // the whole app on one radius system.
            borderRadius: {
                sm: '4px',
                DEFAULT: '6px',
                md: '8px',
                lg: '10px',
                xl: '12px',
                '2xl': '14px',
                '3xl': '20px',
            },

            // Layered ambient elevation: a 1px contact shadow + one or two
            // wide, very low-opacity lifts, all tinted with the ink color
            // instead of pure black so they read as soft rather than grey.
            boxShadow: {
                sm: '0 1px 1px rgb(28 27 25 / 0.03), 0 1px 2px rgb(28 27 25 / 0.04)',
                DEFAULT: '0 1px 2px rgb(28 27 25 / 0.04), 0 2px 6px -1px rgb(28 27 25 / 0.05)',
                md: '0 1px 2px rgb(28 27 25 / 0.04), 0 4px 10px -2px rgb(28 27 25 / 0.06), 0 2px 4px -2px rgb(28 27 25 / 0.03)',
                lg: '0 2px 4px rgb(28 27 25 / 0.03), 0 8px 18px -4px rgb(28 27 25 / 0.07), 0 16px 32px -8px rgb(28 27 25 / 0.05)',
                xl: '0 2px 4px rgb(28 27 25 / 0.04), 0 12px 24px -6px rgb(28 27 25 / 0.1), 0 28px 56px -12px rgb(28 27 25 / 0.1)',
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
                // Toast slide-in (see ui.js).
                'toast-in': {
                    '0%': { opacity: '0', transform: 'translateY(8px) scale(0.98)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) both',
                'flash-once': 'flash-once 0.5s cubic-bezier(0.16, 1, 0.3, 1) both',
                'toast-in': 'toast-in 0.2s cubic-bezier(0.16, 1, 0.3, 1) both',
            },
        },
    },

    plugins: [forms],
};
