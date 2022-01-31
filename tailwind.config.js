module.exports = {
  content: [
    './resources/assets/js/components/**/*.js',
    './resources/assets/js/Layouts/**/*.js',
    './resources/assets/js/Pages/**/*.js',
    './resources/assets/js/*.js',
  ],
  theme: {
    fontFamily: {
      'sans': 'Lato, Arial, Helvetica, sans-serif'
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
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
