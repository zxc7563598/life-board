import CryptoJS from 'crypto-js'

export function setupInterceptors(axiosInstance) {
  const SUCCESS_CODES = [0, 200]
  // 响应成功拦截器
  function resResolve(response) {
    const { data, status, statusText, headers } = response
    if (headers['content-type']?.includes('json')) {
      if (SUCCESS_CODES.includes(data?.code)) {
        return Promise.resolve(data)
      }
      const code = data?.code ?? status
      // 根据code处理对应的操作，并返回处理后的message
      const message = resolveResError(code, data?.message ?? statusText)
      return Promise.reject({
        code,
        message,
        error: data ?? response,
      })
    }
    return Promise.resolve(data ?? response)
  }

  axiosInstance.interceptors.request.use(reqResolve, reqReject)
  axiosInstance.interceptors.response.use(resResolve, resReject)
}

// 请求成功拦截器
function reqResolve(config) {
  // 处理不需要token的请求
  // if (config.needToken !== false) {
  //   const { accessToken } = useAuthStore();
  //   if (accessToken) {
  //     config.headers["X-Auth-Token"] = accessToken;
  //   }
  // }
  // 添加 Accept-Language 头参数
  // config.headers["Accept-Language"] = lStorage.get("locale") || "zh";
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
  switch (code) {
    case 900005:
      // 退出登录

      break
  }
  return message
}
