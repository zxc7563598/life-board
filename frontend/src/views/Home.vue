<template>
  <div class="min-h-100% bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-wrap gap-4">
      <component :is="widgetMap[item]" v-for="(item, index) in widgets" :key="index" />
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import ExpenseCategoryTop10 from '@/components/BillAnalytics/ExpenseCategoryTop10.vue'
import IncomeCategoryTop10 from '@/components/BillAnalytics/IncomeCategoryTop10.vue'
import IncomeExpenseOverview from '@/components/BillAnalytics/IncomeExpenseOverview.vue'
import BillModuleGuide from '@/components/Home/BillModuleGuide.vue'
import NowNews from '@/components/Home/NowNews.vue'
import RealTimeClockCard from '@/components/Home/RealTimeClockCard.vue'
import WelcomeMessage from '@/components/Home/WelcomeMessage.vue'
import Calendar from '@/components/TodoCalendar/Calendar.vue'
import TodoCard from '@/components/TodoList/TodoCard.vue'
import eventBus from '@/utils/event-bus'
import { request } from '@/utils/http/request'

defineOptions({
  name: 'HomeView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'home')

const widgets = ref([])

const widgetMap = {
  NowNews,
  Calendar,
  TodoCard,
  RealTimeClockCard,
  WelcomeMessage,
  BillModuleGuide,
  ExpenseCategoryTop10,
  IncomeCategoryTop10,
  IncomeExpenseOverview,
}

function refreshData() {
  request.post('/home/get-user-widgets').then(({ code, data }) => {
    if (code === 0) {
      widgets.value = data.user_widgets
    }
  })
}

onMounted(() => {
  refreshData()
  eventBus.on('refresh-home-view', refreshData)
})

onBeforeUnmount(() => {
  eventBus.off('refresh-home-view', refreshData)
})
</script>
