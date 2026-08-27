import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appBasePath = env.VITE_APP_BASE_PATH || '/';
    const buildDirectory = env.VITE_BUILD_DIRECTORY || 'build';

    return {
        base: appBasePath,
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
                buildDirectory,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            tailwindcss(),
        ],
        resolve: {
            alias: {
                vue: 'vue/dist/vue.esm-bundler.js',
            },
        },
        build: {
            // Preserve previous hashed files so browsers with cached HTML
            // don't break when they still request the prior asset names.
            emptyOutDir: false,
        },
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
            hmr: {
                host: 'localhost',
                port: 5173,
                protocol: 'ws',
            },
            middlewareMode: false,
        },
    };
});
