<template>
  <div class="h-auto min-w-400px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div class="h-full flex items-center justify-center">
        欢迎你，我的朋友，目前功能还在开发中，敬请期待
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
