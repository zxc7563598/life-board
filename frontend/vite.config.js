import presetIcons from '@unocss/preset-icons'
import vue from '@vitejs/plugin-vue'
import UnoCSS from 'unocss/vite'
import { defineConfig } from 'vite'
import eslintPlugin from 'vite-plugin-eslint'

export default defineConfig({
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
    eslintPlugin({
      fix: true,
      include: ['src/**/*.js', 'src/**/*.vue'],
    }),
  ],
  build: {
    sourcemap: false,
  },
})
