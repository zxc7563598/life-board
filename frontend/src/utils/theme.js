import { darkTheme, lightTheme } from 'naive-ui'

/**
 * 获取当前系统主题对应的 NaiveUI 主题
 * @returns {import('naive-ui').GlobalTheme} 主题对象
 */
export function getSystemTheme() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? darkTheme
    : lightTheme
}

/**
 * 监听系统主题变化
 * @param {(theme: import('naive-ui').GlobalTheme) => void} callback 变化回调
 */
export function watchSystemTheme(callback) {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')

  // 立即执行一次
  callback(getSystemTheme())

  // 监听变化
  const handler = e => callback(e.matches ? darkTheme : lightTheme)
  mediaQuery.addEventListener('change', handler)

  // 返回取消监听函数
  return () => mediaQuery.removeEventListener('change', handler)
}
