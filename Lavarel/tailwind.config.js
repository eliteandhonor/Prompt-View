import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Use Inter and Poppins for a modern, accessible UI
            fontFamily: {
                sans: ['Inter', 'Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'indigo-950': '#1e1b4b',
                'purple-950': '#2a004f',
                'gray-950': '#09090b',
                'violet-accent': '#a78bfa',
                'neon-violet': '#c084fc',
                'fuchsia-glow': '#f0abfc',
                'electric-blue': '#7dd3fc',
                'neon-green': '#39ff14', // Neon green for export buttons
            },
            boxShadow: {
                'neon-violet': '0 0 16px 0 #c084fc, 0 0 32px 4px #a78bfa',
                'glow-purple': '0 2px 24px 0 #a78bfa, 0 0 8px #c084fc',
                'futuristic': '0 0 24px 2px #7c3aed, 0 0 40px 6px #a21caf',
                'neon-green': '0 0 16px 0 #39ff14, 0 0 32px 4px #39ff14', // Neon green shadow
            },
            borderColor: {
                'neon-violet': '#c084fc',
                'electric-blue': '#7dd3fc',
                'neon-green': '#39ff14', // Neon green border
            },
            backgroundImage: {
                'futuristic-gradient': 'linear-gradient(135deg, #1e1b4b 0%, #2a004f 60%, #09090b 100%)',
                'card-gradient': 'linear-gradient(120deg, #2a004f 60%, #1e1b4b 100%)',
                'button-gradient': 'linear-gradient(90deg, #a78bfa 0%, #c084fc 100%)',
            },
        },
    },

    plugins: [forms],
};
