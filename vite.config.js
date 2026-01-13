import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/styles.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            port: process.env.VITE_HMR_PORT || 5173,
            protocol: process.env.VITE_HMR_PROTOCOL || 'ws',
        },
    },
});