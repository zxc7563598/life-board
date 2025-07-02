<template>
  <n-config-provider :theme="theme" :theme-overrides="themeOverrides">
    <n-message-provider>
      <n-layout>
        <div class="h-100vh flex flex-col">
          <AppHeader />
          <n-layout has-sider class="flex-1">
            <n-layout-sider
              v-if="!route.meta.hideLayout" class="h-100%" bordered show-trigger collapse-mode="width"
              :collapsed-width="64" :width="240" :native-scrollbar="false"
            >
              <n-menu :collapsed-width="64" :collapsed-icon-size="22" :options="menuOptions" default-value="home" />
            </n-layout-sider>
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
import { h, onBeforeUnmount, onMounted, ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import AppHeader from '@/components/AppHeader.vue'
import { getSystemTheme, getSystemThemeOverrides, watchSystemTheme, watchSystemThemeOverrides } from './utils/theme'

const theme = ref(getSystemTheme())
const themeOverrides = ref(getSystemThemeOverrides())

let unwatchTheme
let unwatchThemeOverrides
onMounted(() => {
  unwatchTheme = watchSystemTheme((newTheme) => {
    theme.value = newTheme
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

const route = useRoute()
const menuOptions = ref([])
if (!route.meta.hideLayout) {
  menuOptions.value = [
    {
      label: () => h(
        RouterLink,
        {
          to: {
            name: 'HomeView',
          },
        },
        { default: () => '首页' },
      ),
      key: 'home',
      icon: () => h('i', { class: 'i-tabler-home' }),
    },
  ]
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
