<template>
  <div class="h-auto min-w-800px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <n-spin :show="loading">
        <n-calendar
          v-model:value="value" #="{ year, month, date }" class="h-auto min-h-600px"
          @panel-change="handleUpdateValue" @update:value="handleDateClick"
        >
          <div v-if="month === response.month">
            <n-button
              v-for="(item, index) in response.date[date]" :key="index" strong round size="tiny"
              :color="isDark ? color[item.color].dark : color[item.color].light" class="mt-1 w-full overflow-hidden"
              :class="item.completed ? 'line-through' : ''" @click="openDrawer($event, item.id)"
            >
              {{ item.title }}
            </n-button>
          </div>
        </n-calendar>
      </n-spin>
    </n-card>
  </div>
  <TodoDetailDrawer v-model="drawerVisible" :data-id="currentId" :default-timestamp="defaultTimestamp" />
</template>

<script setup>
import { addDays } from 'date-fns'
import { onMounted, ref, watch } from 'vue'
import TodoDetailDrawer from '@/components/TodoList/TodoDetailDrawer.vue'
import { request } from '@/utils/http/request'

const isDark = window.$isDark

const dataInit = ref(true)

const value = ref(addDays(Date.now(), 0).valueOf())

const color = ref([])

const response = ref({
  date: [],
  month: 0,
})
const loading = ref(false)
const lastYear = ref('')
const lastMonth = ref('')
function handleUpdateValue(date) {
  loading.value = true
  lastYear.value = date.year
  lastMonth.value = date.month
  request.post('/todo/get-todo-calendar', { year: date.year, month: date.month }).then(({ code, data }) => {
    if (code === 0) {
      data.color.forEach((item) => {
        color.value[item.key] = item.color
      })
      response.value.date = data.date
      response.value.month = data.month
    }
  }).finally(() => {
    loading.value = false
  })
}

// 细节弹窗
const drawerVisible = ref(false)
const currentId = ref(null)
const defaultTimestamp = ref(null)
function openDrawer(event, id) {
  event.stopPropagation()
  if (id) {
    defaultTimestamp.value = null
    currentId.value = id
    drawerVisible.value = true
  }
}

watch(drawerVisible, (val) => {
  if (val === false) {
    handleUpdateValue({
      year: lastYear.value,
      month: lastMonth.value,
    })
  }
})

// 点击日期
function handleDateClick(timestamp) {
  defaultTimestamp.value = timestamp
  currentId.value = null
  drawerVisible.value = true
}

onMounted(async () => {
  const now = new Date()
  const currentYear = now.getFullYear() // 年份
  const currentMonth = now.getMonth() + 1 // 月份（0-11，所以+1）
  handleUpdateValue({
    year: currentYear,
    month: currentMonth,
  })
})
</script>

<style>
.n-calendar .n-calendar-cell .n-calendar-date {
  margin-top: 0.5rem;
}
</style>
