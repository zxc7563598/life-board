<template>
  <n-layout-sider
    v-if="!route.meta.hideLayout" class="h-100%" bordered show-trigger collapse-mode="width"
    :collapsed-width="64" :width="240" :native-scrollbar="false" :collapsed="collapsed" @collapse="collapsed = true" @expand="collapsed = false"
  >
    <n-menu :collapsed-width="64" :collapsed-icon-size="22" :options="menuOptions" default-value="home" :collapsed="collapsed" />
  </n-layout-sider>
</template>

<script setup>
import { h, ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()

const collapsed = ref(true)

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
