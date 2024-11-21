import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        rollupOptions: {
          output: {
            manualChunks(id) {
              // Define your chunking logic here
              if (id.includes('node_modules')) {
                // Separate vendor libraries into their own chunk
                return 'vendor';
              }
              if (id.includes('resources/js')) {
                // Separate components into their own chunk
                return 'components';
              }
              // Default chunk for other modules
              return 'common';
            }
          }
        }
      },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
