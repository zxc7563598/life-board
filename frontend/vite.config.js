import { fileURLToPath, URL } from 'node:url'
import presetIcons from '@unocss/preset-icons'
import vue from '@vitejs/plugin-vue'
import UnoCSS from 'unocss/vite'
import { defineConfig } from 'vite'
import eslintPlugin from 'vite-plugin-eslint'

const isDev = process.env.NODE_ENV !== 'production'

export default defineConfig({
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  plugins: [
    vue(),
    UnoCSS({
      presets: [
        presetIcons({
          collections: {
            tabler: () => import('@iconify-json/tabler/icons.json').then(i => i.default),
          },
        }),
      ],
    }),
    isDev
    && eslintPlugin({
      fix: true,
      include: ['src/**/*.js', 'src/**/*.vue'],
    }),
  ].filter(Boolean), // 过滤掉 false 插件
  build: {
    sourcemap: false,
  },
})
