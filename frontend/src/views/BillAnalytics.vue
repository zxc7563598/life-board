<template>
  <div class="min-h-100% bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-wrap gap-4">
      <IncomeCategoryTop10 />
      <ExpenseCategoryTop10 />
      <IncomeExpenseOverview />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import ExpenseCategoryTop10 from '@/components/BillAnalytics/ExpenseCategoryTop10.vue'
import IncomeCategoryTop10 from '@/components/BillAnalytics/IncomeCategoryTop10.vue'
import IncomeExpenseOverview from '@/components/BillAnalytics/IncomeExpenseOverview.vue'
// import IncomeExpenseOverview from '@/components/BillAnalytics/IncomeExpenseOverview.vue'
// import MerchantRanking from '@/components/BillAnalytics/MerchantRanking.vue'
// import { request } from '@/utils/http/request'

defineOptions({
  name: 'ProfileView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'profile')

const show = ref([false, false, false])
onMounted(async () => {
  show.value[0] = true
  setTimeout(() => (show.value[1] = true), 200)
  setTimeout(() => (show.value[2] = true), 400)
})
</script>
