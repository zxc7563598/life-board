<template>
  <div class="min-h-100% bg-[#f9fafb] px-4 py-4 dark:bg-[#121212]">
    <div class="flex flex-wrap gap-4">
      <ProfileCard :data="profile.profile_card" class="opacity-0 duration-1500" :class="{ 'opacity-100': show[0] }" />
      <BillmailConfig :data="profile.billmail_config" class="opacity-0 duration-1500" :class="{ 'opacity-100': show[1] }" />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import BillmailConfig from '@/components/Profile/BillmailConfig.vue'
import ProfileCard from '@/components/Profile/ProfileCard.vue'
import { request } from '@/utils/http/request'

defineOptions({
  name: 'ProfileView',
})

const emit = defineEmits(['setDefaultValue'])
emit('setDefaultValue', 'profile')

const profile = ref({})
const show = ref([false, false, false])
onMounted(async () => {
  const res = await request.post('/auth/profile')
  if (res.code === 0) {
    profile.value = res.data
    show.value[0] = true
    setTimeout(() => (show.value[1] = true), 200)
    setTimeout(() => (show.value[2] = true), 400)
  }
})
</script>
