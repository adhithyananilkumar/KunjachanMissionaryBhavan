/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                kb: {
                    bg: '#f9f7f6',
                    'bg-alt': '#f1ecea',
                    primary: '#5a382f',
                    'primary-soft': '#836055',
                    accent: '#a0522d',
                    'accent-soft': '#c77952',
                    text: '#2e211e',
                    'text-dim': '#675a56',
                    surface: '#ffffff',
                    border: '#e6ddda',
                }
            },
            fontFamily: {
                sans: ['Outfit', 'Figtree', 'sans-serif'],
                display: ['Outfit', 'sans-serif'],
            },
            boxShadow: {
                'kb': '0 4px 20px -2px rgba(90, 56, 47, 0.08)',
                'kb-hover': '0 10px 25px -5px rgba(90, 56, 47, 0.12)',
            }
        },
    },
    plugins: [
        import('@tailwindcss/forms'),
    ],
};
