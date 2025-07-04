<template>
  <div class="min-h-100% bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-wrap gap-4">
      <BillTable :search-enums="searchEnums" />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import BillTable from '@/components/Bill/BillTable.vue'
import { request } from '@/utils/http/request'

defineOptions({
  name: 'HomeView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'bill')

const searchEnums = ref()
onMounted(async () => {
  const res = await request.post('/bill/get-bill-search-enums')
  if (res.code === 0) {
    searchEnums.value = res.data
  }
})
</script>
