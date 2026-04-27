/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./app/Views/**/*.php",
        "./public/js/**/*.js"
    ],
    prefix: "tw",
    corePlugins: {
        preflight: false
    },
    theme: {
        extend: {}
    },
    plugins: []
};
