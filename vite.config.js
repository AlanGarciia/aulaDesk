import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { globSync } from 'glob';

const cssFiles = globSync('resources/css/**/*.css');

// SOLO JS que quieres como entry real (evita bootstrap y archivos sueltos problemáticos)
const jsFiles = globSync('resources/js/**/*.js', {
    ignore: [
        'resources/js/bootstrap.js',
        'resources/js/app.js',
    ],
});

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // entry principal obligatorio
                'resources/js/app.js',
                'resources/css/app.css',

                // login si lo usas como entry directo
                'resources/css/login.css',

                // glob controlado
                ...cssFiles,
                ...jsFiles,
            ],
            refresh: true,
        }),
    ],
});