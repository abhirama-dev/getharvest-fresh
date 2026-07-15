/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#2e7d32',
          50:  '#e8f5e9',
          100: '#c8e6c9',
          200: '#a5d6a7',
          300: '#81c784',
          400: '#66bb6a',
          500: '#4caf50',
          600: '#43a047',
          700: '#2e7d32',
          800: '#1b5e20',
          900: '#0d3d13',
        },
        gold: {
          DEFAULT: '#f5b042',
          50:  '#fef8ee',
          100: '#fdedd0',
          200: '#fbd8a1',
          300: '#f9c072',
          400: '#f5b042',
          500: '#ef9a1e',
          600: '#d47f14',
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      boxShadow: {
        card: '0 2px 8px 0 rgba(0,0,0,0.06)',
        'card-hover': '0 8px 24px 0 rgba(0,0,0,0.10)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}