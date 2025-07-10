<template>
  <n-layout-header bordered class="flex px-3 py-1">
    <div>
      LifeBoard · 🗂 你的生活仪表盘
    </div>
    <!-- <div class="ml-a inline-flex">
      <i class="i-tabler-sun h-full" />
      <i class="i-tabler-moon h-full" />
    </div> -->
    <div class="ml-a inline-flex">
      <WidgetsSettings />
    </div>
  </n-layout-header>
</template>

<script setup>
import { useMessage } from 'naive-ui'
import WidgetsSettings from '@/components/WidgetsSettings.vue'

window.$message = useMessage()
</script>
