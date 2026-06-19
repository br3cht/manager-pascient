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
    // garante uma única instância de Vue no bundle (Vue 2 + Vuetify):
    // o app importa vue (ESM) e o vuetify faz require('vue') (CJS) — sem o alias
    // eles resolvem para arquivos diferentes do mesmo pacote, gerando 2 instâncias.
    resolve: {
        alias: {
            vue: 'vue/dist/vue.runtime.esm.js',
        },
        dedupe: ['vue'],
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    ...(isStandalone && {
        // grava direto em public/spa (servido pelo nginx), sem mount extra.
        // publicDir:false evita copiar o public/ do Laravel para dentro do build.
        publicDir: false,
        build: {
            outDir: 'public/spa',
            emptyOutDir: true,
        },
    }),
});
