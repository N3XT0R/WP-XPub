import {defineConfig} from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        react({
            babel: {
                plugins: [
                    ['@wordpress/babel-plugin-makepot', {domain: 'xpub-multi-channel-publisher'}]
                ]
            }
        }),
    ],
    define: {
        'process.env.NODE_ENV': JSON.stringify('production'),
    },
    build: {
        manifest: true,
        outDir: '../dist',
        emptyOutDir: true,
        cssCodeSplit: true,
        rollupOptions: {
            input: './main.jsx',
            output: {
                format: 'es',
                entryFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
                chunkFileNames: '[name].js',
                globals: {
                    react: 'React',
                    'react-dom': 'ReactDOM',
                    '@wordpress/i18n': 'wp.i18n',
                },
            },
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
    },
});
