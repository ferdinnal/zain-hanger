/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Filament/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#5d4037',
                    light:   '#8d6e63',
                },
                secondary: {
                    DEFAULT: '#d4af37',
                    light:   '#f1c40f',
                },
                warm: {
                    50:  '#fdfbf7',
                    100: '#f5f0eb',
                },
            },
            fontFamily: {
                outfit: ['Outfit', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
