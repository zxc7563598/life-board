<template>
  <div class="h-auto min-w-full flex-1">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <n-tabs v-model:value="activeTab" type="segment" animated>
        <n-tab-pane name="Baidu">
          <template #tab>
            <div :class="activeTab === 'Baidu' ? 'text-[#3385ff]' : ''">
              <i class="i-tabler-brand-baidu mr-1" />百度
            </div>
          </template>
          <n-input-group>
            <n-input v-model:value="search" type="text" placeholder="百度一下，你就知道" @keydown.enter="performSearch" />
            <n-button type="primary" ghost color="#3385ff" @click="performSearch">
              百度一下
            </n-button>
          </n-input-group>
        </n-tab-pane>
        <n-tab-pane name="Google">
          <template #tab>
            <div :class="activeTab === 'Google' ? 'text-[#4285F4]' : ''">
              <i class="i-tabler-brand-google mr-1" />Google
            </div>
          </template>
          <n-input-group>
            <n-input v-model:value="search" type="text" placeholder="在Google搜索" @keydown.enter="performSearch" />
            <n-button type="primary" ghost color="#4285F4" @click="performSearch">
              快速搜索
            </n-button>
          </n-input-group>
        </n-tab-pane>
        <n-tab-pane name="Bilibili">
          <template #tab>
            <div :class="activeTab === 'Bilibili' ? 'text-[#00a1d6]' : ''">
              <i class="i-tabler-brand-bilibili mr-1" />哔哩哔哩
            </div>
          </template>
          <n-input-group>
            <n-input v-model:value="search" type="text" placeholder="在哔哩哔哩搜索" @keydown.enter="performSearch" />
            <n-button type="primary" ghost color="#00a1d6" @click="performSearch">
              搜索视频
            </n-button>
          </n-input-group>
        </n-tab-pane>
        <n-tab-pane name="Zhihu">
          <template #tab>
            <div :class="activeTab === 'Zhihu' ? 'text-[#0066ff]' : ''">
              <i class="i-tabler-brand-zhihu mr-1" />知乎
            </div>
          </template>
          <n-input-group>
            <n-input v-model:value="search" type="text" placeholder="在知乎搜索" @keydown.enter="performSearch" />
            <n-button type="primary" ghost color="#0066ff" @click="performSearch">
              搜索问题
            </n-button>
          </n-input-group>
        </n-tab-pane>
      </n-tabs>
    </n-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// 控制当前激活tab
const activeTab = ref('Baidu')
const search = ref('')

function performSearch() {
  if (activeTab.value === 'Baidu') {
    window.open(`https://www.baidu.com/s?wd=${search.value}`, '_blank')
  }
  else if (activeTab.value === 'Google') {
    window.open(`https://www.google.com/search?q=${search.value}`, '_blank')
  }
  else if (activeTab.value === 'Bilibili') {
    window.open(`https://search.bilibili.com/all?keyword=${search.value}`, '_blank')
  }
  else if (activeTab.value === 'Zhihu') {
    window.open(`https://www.zhihu.com/search?q=${search.value}`, '_blank')
  }
}
</script>
