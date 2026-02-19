import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,ts,jsx,tsx}',
    ],
    corePlugins: {
        preflight: false,
        // Prevent collisions with Bootstrap classes.
        // Tailwind generates `.container` and `.collapse` (via visibility utilities),
        // which can override Bootstrap because app.css loads after the CDN.
        container: false,
        visibility: false,
    },
    theme: {
        extend: {},
    },
    plugins: [forms({ strategy: 'class' })],
};
