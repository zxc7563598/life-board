<template>
  <n-config-provider :theme="theme" :theme-overrides="themeOverrides" :class="{ dark: isDark }">
    <n-message-provider>
      <n-layout>
        <div class="h-100vh flex flex-col">
          <AppHeader />
          <n-layout has-sider class="flex-1">
            <AppMenu />
            <n-layout class="h-100%">
              <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                  <component :is="Component" />
                </transition>
              </router-view>
            </n-layout>
          </n-layout>
        </div>
      </n-layout>
    </n-message-provider>
  </n-config-provider>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import AppMenu from '@/components/AppMenu.vue'
import { getSystemTheme, getSystemThemeOverrides, watchSystemTheme, watchSystemThemeOverrides } from './utils/theme'

const theme = ref(getSystemTheme())
const themeOverrides = ref(getSystemThemeOverrides())

const isDark = ref(false)
if (theme.value.name === 'dark') {
  isDark.value = true
}
else {
  isDark.value = false
}
console.warn('初始化', isDark.value)

let unwatchTheme
let unwatchThemeOverrides
onMounted(() => {
  unwatchTheme = watchSystemTheme((newTheme) => {
    theme.value = newTheme
    if (theme.value.name === 'dark') {
      isDark.value = true
    }
    else {
      isDark.value = false
    }
    console.warn('监听', isDark.value)
  })

  unwatchThemeOverrides = watchSystemThemeOverrides((newTheme) => {
    themeOverrides.value = newTheme
  })
})

onBeforeUnmount(() => {
  // 组件卸载时取消监听
  unwatchTheme?.()
  unwatchThemeOverrides?.()
})
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* 路由过渡动画 */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
