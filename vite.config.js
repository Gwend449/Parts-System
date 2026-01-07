import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/script.js', 'resources/css/styles.css'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            // host: process.env.VITE_HMR_HOST || 'avrb1749.ru',
            // port: process.env.VITE_HMR_PORT || 443,
            host: process.env.VITE_HMR_HOST || 'localhost',
            port: process.env.VITE_HMR_PORT || 5173,
            protocol: 'wss',
        },
    },
});
// host: process.env.VITE_HMR_HOST || 'localhost',
//             port: process.env.VITE_HMR_PORT || 5173,