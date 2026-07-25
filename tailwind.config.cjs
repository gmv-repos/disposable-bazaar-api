/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.jsx',
    './resources/**/*.js',
    './resources/**/*.ts',
    './resources/**/*.tsx',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [],

}


// /** @type {import('tailwindcss').Config} */
// module.exports = {
//   theme: {
//     extend: {
//       screens: {
//         '3xl': '1920px',  // 3xl screen starts from 1920px
//         '4xl': '2560px',  // 4xl screen starts from 2560px
//       },
//     },
//   },
//   plugins: [],
// }

