import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { visualizer } from 'rollup-plugin-visualizer';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        process.env.NODE_ENV !== 'production' ? visualizer() : null, // Only enable visualizer in dev
    ].filter(Boolean), // Removes null values from the plugins array
});
