/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './pages/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
  
      colors: {
        secondary: {
          DEFAULT: "#280C12",
          100: "#392428",
          200: "#003e8a",
        },
        primary: {
          DEFAULT: "#FFB624",
          100: "#0064e0",
          200: "#0064e0",
        },
      },
      
    },
  },
  variants: {},
  plugins: [],
}
