<template>
  <div class="h-auto min-w-400px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
      title="今日待办"
    >
      <template #header-extra>
        <n-button strong secondary round type="info" @click="openDrawer(null)">
          添加待办
        </n-button>
      </template>
      <div v-if="todoList.length === 0" class="h-full flex items-center justify-center text-center text-gray-500">
        暂无待办事项
      </div>
      <n-spin :show="todoLoading">
        <div v-if="todoList.length > 0" class="mb-4 text-gray-600">
          <div
            v-for="(item, index) in todoList" :key="index"
            class="mb-2 cursor-pointer rounded-md px-4 py-2 text-white transition-colors duration-300 hover:opacity-90"
            :style="{ background: isDark ? todoColor[item.color].light : todoColor[item.color].dark }"
            @click="openDrawer(item.id)"
          >
            {{ item.title }}
          </div>
        </div>
      </n-spin>
    </n-card>
  </div>
  <TodoDetailDrawer v-model="drawerVisible" :data-id="currentId" />
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import TodoDetailDrawer from '@/components/TodoList/TodoDetailDrawer.vue'
import { request } from '@/utils/http/request'

const isDark = window.$isDark
const dataInit = ref(true)
const todoLoading = ref(false)

const todoColor = ref([])
const todoList = ref([])
const end_at = ref(null)
function getTodoLists() {
  todoLoading.value = true
  const endOfToday = new Date()
  endOfToday.setHours(23, 59, 59, 0)
  end_at.value = endOfToday.getTime()
  // 获取数据
  request.post('/todo/list-todos', { end_at: end_at.value }).then(({ code, data }) => {
    if (code === 0) {
      todoList.value = []
      data.color.forEach((item) => {
        todoColor.value[item.key] = item.color
      })
      data.data.forEach((item) => {
        if (!item.completed) {
          todoList.value.push({
            id: item.id,
            date: item.date,
            title: item.title,
            completed: item.completed,
            color: item.color,
            repeat_type: item.repeat_type,
            repeat_interval: item.repeat_interval,
            repeat_until: item.repeat_until,
          })
        }
      })
    }
  }).finally(() => {
    todoLoading.value = false
    dataInit.value = true
  })
}

// 细节弹窗
const drawerVisible = ref(false)
const currentId = ref(null)
function openDrawer(id) {
  currentId.value = id
  drawerVisible.value = true
}
watch(drawerVisible, (val) => {
  if (val === false) {
    getTodoLists()
  }
})

onMounted(async () => {
  getTodoLists()
})
</script>
