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
      注册账号
    </h2>
    <n-card
      class="max-w-500px w-80% flex flex-col border border-gray-200 rounded-xl py-5 opacity-0 shadow-lg transition-opacity duration-1500"
      :class="{ 'opacity-100': show[2] }"
    >
      <n-form ref="formRef" :model="model" :rules="rules" :show-label="false" :show-feedback="false">
        <n-form-item path="nickname">
          <n-input v-model:value="model.nickname" size="large" round placeholder="昵称" />
        </n-form-item>
        <n-form-item path="username">
          <n-input v-model:value="model.username" size="large" round placeholder="账号" class="mt-5" />
        </n-form-item>
        <n-form-item path="password">
          <n-input
            v-model:value="model.password" type="password" show-password-on="mousedown" size="large"
            round placeholder="密码" class="mt-5"
          />
        </n-form-item>
        <p class="mt-5 pr-2 text-right text-xs">
          已有账号？你可以
          <n-button size="tiny" type="primary" strong quaternary @click="goToLogin">
            登录
          </n-button>
        </p>
        <n-button round strong secondary type="primary" class="mt-5 w-100%" @click="handleLogin">
          注册账号
        </n-button>
      </n-form>
    </n-card>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import bgUrl from '@/assets/register/bg.png'
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
const formRef = ref()
const model = ref({
  nickname: '',
  username: '',
  password: '',
})
const rules = ref({
  nickname: {
    required: true,
    validator(rule, value) {
      if (!value) {
        return new Error('请输入昵称')
      }
      return true
    },
  },
  username: {
    required: true,
    validator(rule, value) {
      if (!value) {
        return new Error('请输入账号')
      }
      return true
    },
  },
  password: {
    required: true,
    validator(rule, value) {
      if (!value) {
        return new Error('请输入密码')
      }
      return true
    },
  },
})

// 执行登录
async function handleLogin() {
  await formRef.value?.validate((errors) => {
    if (errors) {
      errors.forEach((_errors) => {
        _errors.forEach((item) => {
          window.$message?.error(item.message)
        })
      })
      return false
    }
  })
  request.post('/auth/register', {
    nickname: model.value.nickname,
    username: model.value.username,
    password: model.value.password,
  }).then(() => {
    window.$message?.success('注册成功，请进行登录')
    router.push('/login')
  })
}

// 前往登录
function goToLogin() {
  router.push('/login')
}
</script>
