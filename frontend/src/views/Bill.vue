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
  const res = await request.post('/bill/get-bill-search-enums')
  if (res.code === 0) {
    searchEnums.value = res.data
  }
})
</script>
