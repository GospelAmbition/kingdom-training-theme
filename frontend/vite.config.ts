import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';
import { asyncCss } from './vite-plugin-async-css';

// https://vitejs.dev/config/
export default defineConfig({
  // Set base path for WordPress theme deployment
  // In development, use '/' for Vite dev server
  // In production, use the WordPress theme path so dynamic imports work correctly
  base: process.env.NODE_ENV === 'production' 
    ? '/wp-content/themes/kingdom-training-theme/dist/' 
    : '/',
  plugins: [react(), asyncCss()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'index.html'),
      },
      output: {
        manualChunks: {
          // Vendor chunks - cached long-term
          'vendor-react': ['react', 'react-dom', 'react-router-dom'],
          'vendor-query': ['@tanstack/react-query'],
          'vendor-ui': ['lucide-react', 'clsx', 'date-fns'],
          'vendor-helmet': ['react-helmet-async'],
        },
      },
    },
    // Increase chunk size warning limit
    chunkSizeWarningLimit: 600,
  },
  server: {
    port: 3000,
    open: true,
  },
});

