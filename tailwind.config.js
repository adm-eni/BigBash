/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        sunset: {
          50: '#fff3f0',
          100: '#ffe3db',
          200: '#ffc7b8',
          300: '#fca790',
          400: '#f48465',
          500: '#f16741',
          600: '#de461b',
          700: '#c8360e',
          800: '#9e2605',
          900: '#811f03',
        },
      },
    },
  },
  plugins: [],
}

