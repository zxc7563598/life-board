<template>
  <n-config-provider
    :theme="theme" :theme-overrides="themeOverrides" :class="{ dark: isDark }" :locale="zhCN"
    :date-locale="dateZhCN"
  >
    <n-message-provider>
      <n-layout>
        <div class="h-100vh flex flex-col">
          <AppHeader />
          <n-layout has-sider class="flex-1">
            <AppMenu :default-value="defaultValue" />
            <n-layout class="h-100%">
              <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                  <component :is="Component" @set-default-value="onSetDefaultValue" />
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
import { dateZhCN, zhCN } from 'naive-ui'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import AppHeader from '@/components/AppHeader.vue'
import AppMenu from '@/components/AppMenu.vue'
import { getSystemTheme, getSystemThemeOverrides, watchSystemTheme, watchSystemThemeOverrides } from './utils/theme'

// 初始化获取主题信息
const theme = ref(getSystemTheme())
const themeOverrides = ref(getSystemThemeOverrides())
const isDark = ref(false)
window.$isDark = ref(false)
if (theme.value.name === 'dark') {
  isDark.value = true
  window.$isDark.value = true
}
else {
  isDark.value = false
  window.$isDark.value = false
}

// 监听主题变更
let unwatchTheme
let unwatchThemeOverrides
onMounted(() => {
  unwatchTheme = watchSystemTheme((newTheme) => {
    theme.value = newTheme
    if (theme.value.name === 'dark') {
      isDark.value = true
      window.$isDark.value = true
    }
    else {
      isDark.value = false
      window.$isDark.value = false
    }
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

// 菜单默认值处理
const defaultValue = ref('')
function onSetDefaultValue(val) {
  defaultValue.value = val
}
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
