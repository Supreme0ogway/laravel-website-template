import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {//this is for dark and light mode colors
                darkmodeBaseColor: '#f3f4f6',
                darkmodeBaseColorHover: '#ffffff',
                lightmodeBaseColor: '#1f2937',
                lightmodeBaseColorHover: '#374151',
            },
        },
    },

    plugins: [forms, typography],
};
