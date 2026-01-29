import type { Config } from 'tailwindcss'

const config: Config = {
  content: [
    './pages/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          light: '#4BC6FA',
          DEFAULT: '#2F89FF',
          dark: '#1E5FCC',
        },
        secondary: {
          light: '#10B981',
          DEFAULT: '#059669',
          dark: '#047857',
        },
      },
      backgroundImage: {
        'gradient-primary': 'linear-gradient(135deg, #4BC6FA 0%, #2F89FF 100%)',
        'gradient-secondary': 'linear-gradient(135deg, #10B981 0%, #059669 100%)',
      },
    },
  },
  plugins: [],
}
export default config



