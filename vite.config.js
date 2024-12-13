import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
export default defineConfig({
  optimizeDeps: { exclude: ['vue3-carousel'] },
  plugins: [vue()],
  base: process.env.BASE_URL || '/',
  server: {
    port: 3000,
    host: '0.0.0.0',
    disableHostCheck: true
  },
  resolve: {
    alias: {
      '@': '/src',
    },
  },
});

