<template>
  <div class="h-auto min-w-400px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div class="h-full flex flex-col justify-center">
        <p class="text-gray-700 leading-6 dark:text-gray-300">
          LifeBoard 正在慢慢成长，目前还在持续开发中，功能会陆续上线，也许你偶尔会看到一些变化，请多多包涵 🌱
        </p>
        <p class="text-gray-700 leading-6 dark:text-gray-300">
          当前已支持「账单分析」模块 —— 点击右上角 <n-button tertiary circle size="tiny">
            <template #icon>
              <i class="i-tabler-settings" />
            </template>
          </n-button> 即可自由组合卡片组件，打造你自己的生活仪表盘 🧩
        </p>
        <p class="text-gray-700 leading-6 dark:text-gray-300">
          感谢你的耐心与好奇，我们会持续更新，一起让数据更懂生活 💡
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
