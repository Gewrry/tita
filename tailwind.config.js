import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'media', // Use media preference instead of explicit 'class' for now, or just leave it for later.

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                mint: {
                    50: '#F1FBF8',
                    100: '#DDF6ED',
                    200: '#BEEBE1',
                    300: '#90DAC7',
                    400: '#5AC1AA',
                    500: '#10B981', // Primary Mint
                    600: '#1CA074',
                    700: '#1B8060',
                    800: '#18664D',
                    900: '#155541',
                    950: '#064E3B', // Midnight Mint
                },
                beige: {
                    50: '#F7F2E8',
                    100: '#EFE6D8',
                    200: '#D2C2A8',
                    300: '#BFA885',
                    400: '#AA8D63',
                    500: '#8F7149',
                    600: '#735A3B',
                    700: '#57442D',
                    800: '#3B2E1E',
                    900: '#1F1810',
                }
            },
        },
    },

    plugins: [forms],
};
