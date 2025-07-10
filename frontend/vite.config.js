// frontend/vite.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: true,
    port: 5173, // tu wymuszasz konkretny port
    strictPort: true, // ⛔ nie pozwól przeskoczyć na inny
    proxy: {
      '/api': 'http://league-api:8000'
    }
  }
})