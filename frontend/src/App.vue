<template>
  <n-config-provider :theme="theme">
    <n-message-provider>
      <n-layout>
        <div class="h-100vh flex flex-col">
          <n-layout-header bordered>
            LifeBoard · 🗂 你的生活仪表盘
          </n-layout-header>
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
import { getSystemTheme, watchSystemTheme } from './utils/theme'

const theme = ref(getSystemTheme())

let unwatchTheme
onMounted(() => {
  unwatchTheme = watchSystemTheme((newTheme) => {
    theme.value = newTheme
  })
})

onBeforeUnmount(() => {
  // 组件卸载时取消监听
  unwatchTheme?.()
})

const route = useRoute()

const menuOptions = [
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
</script>

<style>
/* 路由过渡动画 */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* 覆盖 NaiveUI 链接样式 */
a {
  text-decoration: none;
}
</style>
