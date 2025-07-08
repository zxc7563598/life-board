<template>
  <div class="min-h-100% bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-wrap gap-4">
      <TextCard />
      <TextCard />
      <IncomeCategoryTop10 />
    </div>
  </div>
</template>

<script setup>
import IncomeCategoryTop10 from '@/components/BillAnalytics/IncomeCategoryTop10.vue'
import TextCard from '@/components/Home/TextCard.vue'

defineOptions({
  name: 'HomeView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'home')
</script>
