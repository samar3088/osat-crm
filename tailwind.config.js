/** @type {import('tailwindcss').Config} */
export default {
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    darkMode: 'class', // Dark mode via class toggle
    theme: {
      extend: {
        // 🎨 Your exact brand colors from the templates
        colors: {
          primary: {
            DEFAULT: '#0e6099',
            dark:    '#0a4a78',
            mid:     '#1a7ec4',
            light:   '#e8f4fd',
            accent:  '#00a8e8',
          },
          dark: {
            DEFAULT: '#0f172a',
            2:       '#1e293b',
          },
          crm: {
            gray:       '#64748b',
            'gray-light':'#94a3b8',
            light:      '#f8fafc',
            border:     '#e2e8f0',
            bg:         '#f0f7ff',
            success:    '#10b981',
            warning:    '#f59e0b',
            danger:     '#ef4444',
          }
        },
        // 🔤 Your exact font
        fontFamily: {
          sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        },
        // 📐 Border radius matching your design
        borderRadius: {
          'input': '12px',
          'card':  '20px',
          'badge': '10px',
        },
        // 📦 Sidebar width as a spacing value
        spacing: {
          'sidebar': '260px',
          'header':  '68px',
        },
        // 💫 Box shadows matching your design
        boxShadow: {
          'card':    '0 20px 60px rgba(14,96,153,0.15)',
          'btn':     '0 8px 25px rgba(14,96,153,0.35)',
          'focus':   '0 0 0 4px rgba(14,96,153,0.09)',
        },
        // 🌊 Animations from your dashboard
        animation: {
          'float': 'float 6s ease-in-out infinite',
          'fill':  'fill 2s ease-in-out',
        },
        keyframes: {
          float: {
            '0%, 100%': { transform: 'translateY(0)' },
            '50%':      { transform: 'translateY(-12px)' },
          },
          fill: {
            'from': { width: '0' },
          }
        },
      },
    },
    plugins: [],
  }