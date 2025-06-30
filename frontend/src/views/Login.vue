<template>
  <n-card title="登录" class="mx-auto mt-10 max-w-md">
    这是登录
  </n-card>
</template>

<script setup>
import { useMessage } from 'naive-ui'
import { request } from '../utils/http/request'

defineOptions({
  name: 'LoginView',
})

const message = useMessage()
request.post('/test', { name: 'su' }).then((res) => {
  console.warn(res)
  message?.success('登录成功')
})
</script>
