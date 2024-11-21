import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    presets:[
        require("./vendor/power-components/livewire-powergrid/tailwind.config.js"),
    ],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./node_modules/flowbite/**/*.js",
        './app/Livewire/**/*Table.php',
        './vendor/power-components/livewire-powergrid/resources/views/**/*.php',
        './vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php'
    ],
    safelist: [
        'bg-cyan-500', // untuk class created
        'bg-red-500', // untuk class rejected atau canceled
        'bg-blue-700', // untuk class CHECKED
        'bg-green-600', // untuk class SIGNED
        'bg-amber-600', // untuk class REVISED
        'bg-teal-600', // untuk class SEND
        'bg-indigo-700', //untuk class CONFIRM
        'bg-lime-500', // Untuk DONE
      ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        require('flowbite/plugin')({
            datatables: true,
        }),

    ],
};
