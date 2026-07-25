import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/React/App.jsx',
            refresh: true,
        }),
        react(),
    ], build: {
        manifest: true,
        outDir: 'public/build', // Ensure this matches the directory where you're looking for the manifest
    },
});
