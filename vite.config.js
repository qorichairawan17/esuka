import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Optimize build output
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true,
            },
        },
        // Code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios'],
                },
            },
        },
        // Chunk size warnings
        chunkSizeWarningLimit: 600,
        // Source maps for debugging (disable in production)
        sourcemap: false,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['axios'],
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
