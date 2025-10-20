import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(), // add Vue plugin
    ],
    define: {
        __VUE_OPTIONS_API__: true,          // enable Options API if you use it
        __VUE_PROD_DEVTOOLS__: false,       // disable devtools in production
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
    },
});
