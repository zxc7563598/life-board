<template>
  <div class="h-full flex overflow-hidden bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-1 flex-wrap gap-4">
      <TaskPanel />
    </div>
  </div>
</template>

<script setup>
import TaskPanel from '@/components/TodoList/TaskPanel.vue'

defineOptions({
  name: 'TodoListView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'todo-list')
</script>
