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
import { request } from '@/utils/http/request'

defineOptions({
  name: 'ProfileView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'profile')

const show = ref([false, false, false])
onMounted(async () => {
  request.post('/bill/has-user-imap-config').then(({ code, data }) => {
    if (code === 0) {
      if (!data.billmail_config) {
        window.$message?.warning('若要使用该功能，请前往「个人配置」「账单监听邮箱配置」中设置监听邮箱')
      }
      if (!data.imap_config) {
        window.$message?.warning('账单监听邮箱配置异常，请前往「个人配置」「账单监听邮箱配置」中重新设置')
      }
    }
  })
  show.value[0] = true
  setTimeout(() => (show.value[1] = true), 200)
  setTimeout(() => (show.value[2] = true), 400)
})
</script>
