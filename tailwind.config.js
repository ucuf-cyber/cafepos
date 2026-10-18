/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                'display': ['"Fraunces"', 'serif'],
                'sans': ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                'mono': ['"JetBrains Mono"', 'monospace'],
            },
            colors: {
                cream: {
                    50: '#FAF6F0',
                    100: '#F5EDE0',
                    200: '#E8D9C0',
                },
                coffee: {
                    50: '#FAF6F0',
                    100: '#EAD9C2',
                    200: '#C8A57E',
                    300: '#A07B4D',
                    400: '#7A5635',
                    500: '#5D3F26',
                    600: '#4A2F1B',
                    700: '#3D2B22',
                    800: '#2A1F1A',
                    900: '#1A1210',
                },
                caramel: {
                    400: '#D4944E',
                    500: '#C4813D',
                    600: '#A66A2C',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'slide-up': 'slideUp 0.35s cubic-bezier(.4,0,.2,1)',
                'slide-down': 'slideDown 0.35s cubic-bezier(.4,0,.2,1)',
                'shimmer': 'shimmer 1.6s linear infinite',
                'bounce-soft': 'bounceSoft 0.5s ease',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                slideUp: { '0%': { transform: 'translateY(20px)', opacity: 0 }, '100%': { transform: 'translateY(0)', opacity: 1 } },
                slideDown: { '0%': { transform: 'translateY(-20px)', opacity: 0 }, '100%': { transform: 'translateY(0)', opacity: 1 } },
                shimmer: { '0%': { backgroundPosition: '-1000px 0' }, '100%': { backgroundPosition: '1000px 0' } },
                bounceSoft: { '0%, 100%': { transform: 'scale(1)' }, '50%': { transform: 'scale(1.08)' } },
                pulseSoft: { '0%, 100%': { opacity: 1 }, '50%': { opacity: 0.5 } },
            },
        },
    },
    plugins: [],
};
