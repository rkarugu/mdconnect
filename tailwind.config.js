const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');
const typography = require('@tailwindcss/typography');
const aspectRatio = require('@tailwindcss/aspect-ratio');

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js', // For Alpine.js or plain JS interactions
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: '#4A90E2',
        secondary: '#50E3C2',
      },
    },
  },

  plugins: [
    forms,
    typography,
    aspectRatio,
  ],
};
