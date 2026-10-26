module.exports = {
  content: ['./resources/assets/js/{components,contexts,Layouts,Pages,hooks}/**/*.{jsx,tsx}'],
  theme: {
    fontFamily: { 'sans': 'Lato, Arial, Helvetica, sans-serif' },
    extend: {
      colors: {
        'brand-purple-dark': '#200142',
        'brand-light-pink': '#e5e3e8',
        'brand-off-white': '#f2f1f3',
        'brand-blue': '#21a7eb',
      },
      width: { '250px': '250px' },
      transitionTimingFunction: {
        'in-back': 'cubic-bezier(0.36, 0, 0.66, -0.56)',
        'out-back': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
      },
      typography: (theme) => ({
        DEFAULT: {
          css: {
            a: { color: theme('colors.purple.500') },
            h1: { fontWeight: 300 },
            h2: { fontWeight: 300 },
            h3: { fontWeight: 300 },
		  	ul: { marginTop: 0 },
		  	'li > p': { margin: 0 },
          }
        }
      }),
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
