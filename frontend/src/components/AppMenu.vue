<template>
  <n-layout-sider
    v-if="!route.meta.hideLayout && menuDefaultValue" class="h-100%" bordered show-trigger
    collapse-mode="width" :collapsed-width="64" :width="240" :native-scrollbar="false" :collapsed="collapsed"
    @collapse="collapsed = true" @expand="collapsed = false"
  >
    <n-menu
      :collapsed-width="64" :collapsed-icon-size="22" :options="menuOptions" :default-value="menuDefaultValue"
      :collapsed="collapsed"
    />
  </n-layout-sider>
</template>

<script setup>
import { h, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { request } from '@/utils/http/request'

const props = defineProps({
  defaultValue: String,
})

const menuDefaultValue = ref('')
watch(
  () => props.defaultValue,
  (val) => {
    if (val && Object.keys(val).length > 0) {
      menuDefaultValue.value = val
    }
  },
  { immediate: true },
)

const route = useRoute()
const router = useRouter()

// 设置菜单默认折叠
const collapsed = ref(true)

// 初始化菜单信息
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
    {
      label: () => h(
        RouterLink,
        {
          to: {
            name: 'BillView',
          },
        },
        { default: () => '我的账单' },
      ),
      key: 'bill',
      icon: () => h('i', { class: 'i-tabler-receipt' }),
    },
    {
      label: () => h(
        RouterLink,
        {
          to: {
            name: 'BillAnalyticsView',
          },
        },
        { default: () => '账单分析' },
      ),
      key: 'bill-analytics',
      icon: () => h('i', { class: 'i-tabler-receipt' }),
    },
    {
      key: 'divider-1',
      type: 'divider',
    },
    {
      label: () => h(
        RouterLink,
        {
          to: {
            name: 'ProfileView',
          },
        },
        { default: () => '个人配置' },
      ),
      key: 'profile',
      icon: () => h('i', { class: 'i-tabler-settings' }),
    },
    {
      label: '退出登录',
      key: 'logout',
      icon: () => h('i', { class: 'i-tabler-logout' }),
      onClick: () => {
        logout()
      },
    },
  ]
}

// 退出登录
function logout() {
  request.post('/auth/logout', {
    refresh_token: localStorage.getItem('refresh_token') || '',
  }).then(() => {
    localStorage.removeItem('token')
    localStorage.removeItem('refresh_token')
    window.$message?.success('已退出登录')
    router.push('/login')
  })
}
</script>
