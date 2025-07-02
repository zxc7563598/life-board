<template>
  <div class="flex flex-col items-center justify-center">
    <n-image
      width="320" :src="bgUrl" class="mt-5 rounded-xl opacity-0 transition-opacity duration-800"
      :class="{ 'opacity-100': show[0] }"
    />
    <h2
      class="tracking-light px-4 pb-5 pt-5 text-center text-[28px] font-bold leading-tight opacity-0 transition-opacity duration-1000"
      :class="{ 'opacity-100': show[1] }"
    >
      欢迎回来
    </h2>
    <n-card
      class="max-w-500px w-80% flex flex-col border border-gray-200 rounded-xl py-5 opacity-0 shadow-lg transition-opacity duration-1500"
      :class="{ 'opacity-100': show[2] }"
    >
      <n-input v-model:value="username" size="large" round placeholder="账号" />
      <n-input v-model:value="password" size="large" round placeholder="密码" class="mt-5" />
      <p class="mt-5 pr-2 text-right text-xs">
        没有账号？你可以
        <n-button size="tiny" type="primary" strong quaternary @click="goToRegister">
          注册
        </n-button>
      </p>
      <n-button round strong secondary type="primary" class="mt-5 w-100%" @click="handleLogin">
        登录
      </n-button>
    </n-card>
  </div>
</template>

<script setup>
import Bowser from 'bowser'
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import bgUrl from '@/assets/login/bg.png'
import { request } from '@/utils/http/request'

defineOptions({
  name: 'LoginView',
})

const router = useRouter()

// 页面动画加载
const show = ref([false, false, false])
onMounted(() => {
  show.value[0] = true
  setTimeout(() => (show.value[1] = true), 200)
  setTimeout(() => (show.value[2] = true), 400)
})

// 表单参数
const username = ref('')
const password = ref('')

// 执行登录
function handleLogin() {
  const browser = Bowser.getParser(window.navigator.userAgent)
  request.post('/auth/login', {
    username: username.value,
    password: password.value,
    browser_name: browser.getBrowserName(),
    browser_version: browser.getBrowserVersion(),
    engine_name: browser.getEngineName(),
    os_name: browser.getOSName(),
    os_version: browser.getOSVersion(),
    platform_type: browser.getPlatformType(),
    ua: browser.getUA(),
  }).then((res) => {
    const access_token = res.data.access_token
    const refresh_token = res.data.refresh_token
    localStorage.setItem('token', access_token)
    localStorage.setItem('refresh_token', refresh_token)
    window.$message?.success('登录成功')
  })
}

// 前往注册
function goToRegister() {
  router.push('/register')
}
</script>
