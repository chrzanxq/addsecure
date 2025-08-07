import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue2';

export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: './public/assets',
    emptyOutDir: false,
    rollupOptions: {
      input: './resources/main.ts',
      output: {
        manualChunks: false,
        inlineDynamicImports: true,
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      },
    },
  },
});
