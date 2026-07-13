// NFR-08 — Metronic 9.5.0 integrated via Vite + @tailwindcss/vite
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

const METRONIC_SRC = new URL('./resources/metronic', import.meta.url).pathname;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@metronic': METRONIC_SRC,
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
