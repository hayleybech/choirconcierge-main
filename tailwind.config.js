const colors = require('tailwindcss/colors')
module.exports = {
  purge: [
    // './resources/**/*.js',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    fontFamily: {
      'sans': 'Lato, Arial, Helvetica, sans-serif'
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      white: colors.white,
      gray: colors.coolGray,
      indigo: colors.indigo,
      purple: colors.purple,
      red: colors.red,
      yellow: colors.amber,
      green: colors.emerald,
      pink: colors.pink,
    },
    extend: {
      colors: {
        'brand-purple-dark': '#200142',
        'brand-light-pink': '#e5e3e8',
        'brand-off-white': '#f2f1f3',
        'brand-blue': '#21a7eb',
      },
      width: {
        '250px': '250px',
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
