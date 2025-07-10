<template>
  <div class="h-auto min-w-400px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div class="h-full flex flex-col justify-center">
        <p class="text-gray-700 leading-6 dark:text-gray-300">
          📊 账单分析功能上线啦！
        </p>
        <p class="text-gray-700 leading-6 dark:text-gray-300">
          现在可以自动生成支付宝 / 微信的收支报告啦！
        </p>
        <p class="text-gray-700 leading-6 dark:text-gray-300">
          只需在【个人配置】中设置一个可用的监听邮箱，随后在支付宝或微信中开具“交易流水证明”，并发送到该邮箱，系统将自动解析并生成账单分析报告。
        </p>
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

const dataInit = ref(false)

onMounted(async () => {
  dataInit.value = true
})
</script>
