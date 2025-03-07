import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import * as fs from 'fs/promises';
const constants = fs.constants;

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
