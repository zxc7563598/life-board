<template>
  <div class="min-h-100% bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-wrap gap-4">
      <Calendar />
    </div>
  </div>
</template>

<script setup>
import Calendar from '@/components/TodoCalendar/Calendar.vue'

defineOptions({
  name: 'TodoCalendarView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'todo-calendar')
</script>
