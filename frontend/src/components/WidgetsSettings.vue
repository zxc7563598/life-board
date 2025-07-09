<template>
  <n-button tertiary circle size="tiny" @click="openEditDrawer">
    <template #icon>
      <i class="i-tabler-settings" />
    </template>
  </n-button>
  <n-drawer v-model:show="active" height="80%" placement="top" :class="{ dark: isDark }">
    <n-drawer-content title="首页组件管理" body-class="bg-[#f9fafb] dark:bg-[#121212]">
      <div class="h-100% flex flex-col gap-4">
        <n-card
          class="w-full border border-gray-200 rounded-xl bg-[#f9fafb] shadow-lg transition dark:border-gray-700 dark:bg-[#121212] dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
        >
          此页面用于配置首页展示的组件内容与顺序：
          <ul class="mt-2 list-disc pl-4 text-sm text-gray-600 dark:text-gray-400">
            <li>左侧为平台支持的所有首页组件。</li>
            <li>将所需组件从左侧拖拽至右侧，即可添加至首页展示列表。</li>
            <li>右侧组件支持拖动排序，顺序将同步用于首页展示。</li>
            <li>配置完成后，点击底部【保存】按钮提交更改。</li>
          </ul>
        </n-card>
        <div class="min-h-0 flex flex-1 gap-4">
          <n-card
            class="flex-1 border border-gray-200 rounded-xl bg-[#f9fafb] shadow-lg transition dark:border-gray-700 dark:bg-[#121212] dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
            content-class="h-100%"
          >
            <div class="h-100% flex flex-col">
              <div class="mb-4 text-[15px] font-bold leading-tight">
                全部组件 {{ widgetsList.length }}
              </div>
              <div class="min-h-0 flex-1 overflow-hidden overflow-y-auto p-2">
                <draggable
                  v-model="widgetsList" item-key="id" :sort="false"
                  :group="{ name: 'tasks', pull: 'clone', put: false }" :clone="cloneItem"
                  class="min-h-100% min-w-100%"
                >
                  <template #item="{ element }">
                    <n-card
                      class="mb-2 w-full border border-gray-200 rounded-xl bg-[#f9fafb] shadow-lg transition dark:border-gray-700 hover:border-blue-400 dark:bg-[#121212] hover:bg-[#eef4ff] dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)] hover:shadow-xl dark:hover:border-blue-500 dark:hover:bg-[#1e1e1e] dark:hover:shadow-[0_4px_16px_rgba(0,0,0,0.6)]"
                    >
                      <div class="text-base font-medium">
                        {{ element.name }}
                      </div>
                      <div class="mt-1 text-sm text-gray-500">
                        {{ element.description }}
                      </div>
                    </n-card>
                  </template>
                </draggable>
              </div>
            </div>
          </n-card>
          <n-card
            class="flex-1 border border-gray-200 rounded-xl bg-[#f9fafb] shadow-lg transition dark:border-gray-700 dark:bg-[#121212] dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
            content-class="h-100%"
          >
            <div class="h-100% flex flex-col">
              <div class="mb-4 text-[15px] font-bold leading-tight">
                我的组件 {{ userWidgetsList.length }}
              </div>
              <div class="min-h-0 flex-1 overflow-hidden overflow-y-auto p-2">
                <draggable
                  v-model="userWidgetsList" item-key="id" group="tasks"
                  class="min-h-100% min-w-100%"
                >
                  <template #item="{ element, index }">
                    <n-card
                      class="mb-2 w-full border border-gray-200 rounded-xl bg-[#f9fafb] shadow-lg transition dark:border-gray-700 hover:border-blue-400 dark:bg-[#121212] hover:bg-[#eef4ff] dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)] hover:shadow-xl dark:hover:border-blue-500 dark:hover:bg-[#1e1e1e] dark:hover:shadow-[0_4px_16px_rgba(0,0,0,0.6)]"
                    >
                      <div class="text-base font-medium">
                        {{ element.name }}
                      </div>
                      <div class="mt-1 text-sm text-gray-500">
                        {{ element.description }}
                      </div>
                      <n-button
                        circle quaternary size="tiny" class="absolute right-2 top-2 z-10"
                        @click.stop="removeItem(index)"
                      >
                        <template #icon>
                          <i class="i-tabler-x" />
                        </template>
                      </n-button>
                    </n-card>
                  </template>
                </draggable>
              </div>
            </div>
          </n-card>
        </div>
        <n-button strong secondary round type="primary" class="w-full" @click="saveWidgets">
          保存
        </n-button>
      </div>
    </n-drawer-content>
  </n-drawer>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import draggable from 'vuedraggable'
import eventBus from '@/utils/event-bus'
import { request } from '@/utils/http/request'

const route = useRoute()

const isDark = window.$isDark

watch(isDark, (dark) => {
  isDark.value = dark
})

const active = ref(false)

const widgetsList = ref([])
const widgets = ref([])
const userWidgetsList = ref([])

// 打开弹窗
function openEditDrawer() {
  widgetsList.value = []
  widgets.value = []
  userWidgetsList.value = []
  active.value = true
  request.post('/home/get-user-widgets').then(({ code, data }) => {
    if (code === 0) {
      data.dashboard_widgets.forEach((item) => {
        widgetsList.value.push({
          widget_id: item.id,
          id: item.id,
          name: item.name,
          component_key: item.component_key,
          description: item.description,
        })
        widgets.value[item.component_key] = {
          widget_id: item.id,
          id: item.id,
          name: item.name,
          component_key: item.component_key,
          description: item.description,
        }
      })
      data.user_widgets.forEach((component_key) => {
        if (widgets.value[component_key]) {
          userWidgetsList.value.push(widgets.value[component_key])
        }
      })
    }
  })
}

// 加入组件
function cloneItem(item) {
  return {
    ...item,
    id: Date.now() + Math.random(), // 保证唯一性
  }
}

// 删除组件
function removeItem(index) {
  userWidgetsList.value.splice(index, 1)
}

// 存储组件
const widgetsParams = ref([])
async function saveWidgets() {
  widgetsParams.value = []
  userWidgetsList.value.forEach((item) => {
    widgetsParams.value.push({
      widget_id: item.widget_id,
    })
  })
  const { code } = await request.post('/home/save-user-widgets', {
    widgets: widgetsParams.value,
  })
  if (code === 0) {
    window.$message?.success('存储成功')
    active.value = false
    if (route.name === 'HomeView') {
      eventBus.emit('refresh-home-view')
    }
  }
}
</script>
