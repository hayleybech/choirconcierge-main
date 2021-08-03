import '../public/css/app.css'
// import '../public/css/style.css'
import '../public/vendor/fontawesome-pro/css/all.min.css'

export const parameters = {
  actions: { argTypesRegex: "^on[A-Z].*" },
  controls: {
    matchers: {
      color: /(background|color)$/i,
      date: /Date$/,
    },
  },
}