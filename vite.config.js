import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import inject from '@rollup/plugin-inject';

export default defineConfig({
    optimizeDeps: {
        exclude: [
            // '@ironsoftware_ironpdf'
        ]
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/map.js',
                'resources/js/stripe.js',
            ],
            refresh: [
                'app/**',
                'routes/**',
                'resources/views/**',
            ],
        }),
    ],
    resolve: {
        alias: {
            $: 'jQuery'
        }
    },
});
