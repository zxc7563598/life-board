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
 * 获取当前系统主题对应的 NaiveUI 主题配色
 */
export function getSystemThemeOverrides() {
  return window.matchMedia('(prefers-color-scheme: dark)').matches
    ? {
        common: {
          primaryColor: '#4098fc',
          primaryColorHover: '#63aefc',
          primaryColorPressed: '#1060c9',
          primaryColorSuppl: '#4098fc',
        },
      }
    : {
        common: {
          primaryColor: '#2080f0',
          primaryColorHover: '#4098fc',
          primaryColorPressed: '#1060c9',
          primaryColorSuppl: '#2080f0',
        },
      }
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

/**
 * 监听系统主题变化（返回 NaiveUI 的主题配色 overrides）
 * @param {(overrides: Record<string, any>) => void} callback 变化回调
 * @returns {() => void} 取消监听函数
 */
export function watchSystemThemeOverrides(callback) {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')

  // 立即执行一次
  callback(getSystemThemeOverrides())

  // 监听变化
  const handler = e => callback(e.matches
    ? {
        common: {
          primaryColor: '#4098fc',
          primaryColorHover: '#63aefc',
          primaryColorPressed: '#1060c9',
          primaryColorSuppl: '#4098fc',
        },
      }
    : {
        common: {
          primaryColor: '#2080f0',
          primaryColorHover: '#4098fc',
          primaryColorPressed: '#1060c9',
          primaryColorSuppl: '#2080f0',
        },
      })

  mediaQuery.addEventListener('change', handler)

  // 返回取消监听函数
  return () => mediaQuery.removeEventListener('change', handler)
}
