/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./server.js",
    ],
    theme: {
        extend: {
            colors: {
                bg: "#FFFFFF",
                "bg-muted": "#F7F8FA",
                border: "#E5E7EB",
                text: "#101828",
                "text-muted": "#667085",
                accent: "#F97316",
                "accent-hover": "#EA580C",
                success: "#067647",
                warning: "#B54708",
                danger: "#B42318",
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                mono: ['JetBrains Mono', 'Menlo', 'monospace'],
            },
            borderRadius: {
                'card': '12px',
                'control': '8px',
            },
            boxShadow: {
                'card': '0 1px 2px rgba(16,24,40,.06)',
                'card-lg': '0 4px 12px rgba(16,24,40,.08)',
            },
            maxWidth: {
                'container': '1120px',
            },
        },
    },
    plugins: [],
}
