import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { globSync } from 'glob';

const cssFiles = globSync('resources/css/**/*.css');
const jsFiles = globSync('resources/js/**/*.js', {
    ignore: [
        'resources/js/bootstrap.js',   // importat per app.js, no és entry
    ],
});

export default defineConfig({
    plugins: [
        laravel({
            input: [
                ...cssFiles,
                ...jsFiles,
            ],
            refresh: true,
        }),
    ],
});