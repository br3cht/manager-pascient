import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue2';

const isStandalone = process.env.BUILD_MODE === 'standalone';

export default defineConfig({
    plugins: [
        vue(),
        ...(!isStandalone
            ? [laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true })]
            : []),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    ...(isStandalone && {
        build: {
            outDir: 'dist',
            emptyOutDir: true,
        },
    }),
});
