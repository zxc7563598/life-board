<template>
  <div class="inline-flex">
    <n-card
      class="max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div>
        <div class="mb-3 text-base font-medium">
          账号信息
        </div>
        <n-form ref="formRef" :model="model" :rules="rules" class="text-sm text-gray-500" :show-label="false" :show-feedback="true" label-placement="left" label-width="auto" require-mark-placement="right-hanging" size="small">
          <n-form-item path="nickname" label="昵称">
            <n-input v-model:value="model.nickname" size="medium" round placeholder="昵称" />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>仅用于系统内的展示称呼，不影响账号登录</p>
              </div>
            </template>
          </n-form-item>
          <n-form-item path="username" label="账号" class="mt-3">
            <n-input v-model:value="model.username" size="medium" round placeholder="账号" />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>修改账号将导致当前会话失效，需使用新账号和密码重新登录</p>
              </div>
            </template>
          </n-form-item>
          <n-form-item path="password" label="密码" class="mt-3">
            <n-input v-model:value="model.password" size="medium" round placeholder="新密码" />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>如无需修改密码，可留空</p>
                <p>若修改密码，当前会话将失效，需使用新密码重新登录</p>
              </div>
            </template>
          </n-form-item>
          <n-button round strong secondary type="primary" class="mt-3 w-100%" :loading="loading" @click="handleSave">
            保存
          </n-button>
        </n-form>
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { defineProps, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '@/utils/http/request'

const props = defineProps({
  data: Object,
})

const router = useRouter()

// 表单参数
const loading = ref(false)
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
})

// 处理父组件传值
watch(
  () => props.data,
  (val) => {
    if (val && Object.keys(val).length > 0) {
      model.value.nickname = val.nickname
      model.value.username = val.username
    }
  },
  { immediate: true },
)

async function handleSave() {
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
  loading.value = true
  request.post('/auth/set-profile', {
    nickname: model.value.nickname,
    username: model.value.username,
    password: model.value.password,
  }).then((res) => {
    loading.value = false
    window.$message?.success('保存成功')
    if (res.data.login) {
      router.push('/login')
    }
  })
}
</script>
