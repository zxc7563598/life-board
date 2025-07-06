import Bowser from 'bowser'
import CryptoJS from 'crypto-js'
import { request } from './request'

let isRefreshing = false
let refreshSubscribers = []

function onRefreshed(newToken) {
  refreshSubscribers.forEach(cb => cb(newToken))
  refreshSubscribers = []
}

function addRefreshSubscriber(callback) {
  refreshSubscribers.push(callback)
}

export function setupInterceptors(axiosInstance) {
  const SUCCESS_CODES = [0, 200]
  // 响应成功拦截器
  async function resResolve(response) {
    const { data, status, statusText, headers, config } = response
    // 如果响应为 JSON 类型
    if (headers['content-type']?.includes('json')) {
      if (SUCCESS_CODES.includes(data?.code)) {
        return Promise.resolve(data)
      }
      const code = data?.code ?? status
      if ([800001, 800002].includes(code)) {
        if (!isRefreshing) {
          isRefreshing = true

          const browser = Bowser.getParser(window.navigator.userAgent)

          return request
            .post('/auth/refresh', {
              refresh_token: localStorage.getItem('refresh_token') || '',
              browser_name: browser.getBrowserName(),
              browser_version: browser.getBrowserVersion(),
              engine_name: browser.getEngineName(),
              os_name: browser.getOSName(),
              os_version: browser.getOSVersion(),
              platform_type: browser.getPlatformType(),
              ua: browser.getUA(),
            })
            .then((res) => {
              const access_token = res.data.access_token
              const refresh_token = res.data.refresh_token
              localStorage.setItem('token', access_token)
              localStorage.setItem('refresh_token', refresh_token)
              // 更新 Authorization 默认值
              axiosInstance.defaults.headers.Authorization = `Bearer ${access_token}`
              onRefreshed(access_token)
              return request(config)
            })
            .catch((err) => {
              localStorage.removeItem('token')
              localStorage.removeItem('refresh_token')
              location.href = '/login'
              return Promise.reject({
                code,
                message: '登录已过期，请重新登录',
                error: err,
              })
            })
            .finally(() => {
              isRefreshing = false
            })
        }

        // 如果正在刷新中，挂起请求，等待刷新完成后再重试
        return new Promise((resolve) => {
          addRefreshSubscriber((newToken) => {
            config.headers.Authorization = `Bearer ${newToken}`
            resolve(request(config))
          })
        })
      }

      if ([800006, 800007, 800008].includes(code)) {
        localStorage.removeItem('token')
        localStorage.removeItem('refresh_token')
        location.href = '/login'
        return Promise.reject({
          code,
          message: '登录已失效，请重新登录',
          error: data,
        })
      }
      // 其他错误统一处理
      const message = resolveResError(code, data?.message ?? statusText)
      return Promise.reject({ code, message, error: data ?? response })
    }
    return Promise.resolve(data ?? response)
  }
  axiosInstance.interceptors.request.use(reqResolve, reqReject)
  axiosInstance.interceptors.response.use(resResolve, resReject)
}

// 请求成功拦截器
function reqResolve(config) {
  // 处理不需要token的请求
  const token = localStorage.getItem('token') || ''
  // 如果有 token，就添加到请求头 Authorization 中
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  // 添加 Accept-Language 头参数
  config.headers['Accept-Language'] = localStorage.getItem('locale') || 'zh'
  // 加密请求参数
  if (config.data === undefined) {
    config.data = {}
  }
  const secretKey = CryptoJS.enc.Utf8.parse(import.meta.env.VITE_AES_KEY) // 16字节的密钥
  const iv = CryptoJS.enc.Utf8.parse(import.meta.env.VITE_AES_IV) // 16字节的初始化向量
  const encrypted = CryptoJS.AES.encrypt(
    JSON.stringify(removeEmptyValues(config.data)),
    secretKey,
    {
      iv,
      mode: CryptoJS.mode.CBC,
      padding: CryptoJS.pad.Pkcs7,
    },
  )
  const en_data = encrypted.toString()
  const timestamp = Math.floor(Date.now() / 1000)
  const signKey = import.meta.env.VITE_SIGN_KEY
  const sign = CryptoJS.MD5(signKey + timestamp).toString()
  config.data = { en_data, timestamp, sign }
  return config
}

// 请求失败拦截器
function reqReject(error) {
  return Promise.reject(error)
}

// 响应失败拦截器
async function resReject(error) {
  if (!error || !error.response) {
    const code = error?.code
    /** 根据code处理对应的操作，并返回处理后的message */
    const message = resolveResError(code, error.message)
    return Promise.reject({ code, message, error })
  }
  const { data, status } = error.response
  const code = data?.code ?? status
  // 根据code处理对应的操作，并返回处理后的message
  const message = resolveResError(code, data?.message ?? error.message)
  return Promise.reject({
    code,
    message,
    error: error.response?.data || error.response,
  })
}

// 参数过滤
function removeEmptyValues(obj) {
  return Object.keys(obj).reduce((acc, key) => {
    switch (obj[key]) {
      case null:
      case undefined:
      case '':
        break
      default:
        if (typeof obj[key] == 'object' && !Object.keys(obj[key]).length) {
          return acc
        }
        acc[key] = obj[key]
        break
    }
    return acc
  }, {})
}

// 特定异常处理
function resolveResError(code, message) {
  // 如果需要特殊处理
  switch (code) {
    case 'xxx':
      break
  }
  window.$message?.warning(message)
  return message
}
