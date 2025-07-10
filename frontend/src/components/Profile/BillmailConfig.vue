<template>
  <div class="inline-flex">
    <n-card
      class="max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div>
        <div class="mb-3 text-base font-medium">
          账单监听邮箱配置
        </div>
        <n-form
          ref="formRef" :model="model" :rules="rules" class="text-sm text-gray-500" :show-label="false"
          :show-feedback="true" label-placement="left" label-width="auto"
          require-mark-placement="right-hanging" size="small"
        >
          <n-form-item path="mail_providers" label="服务商">
            <n-select
              v-model:value="model.mail_providers" :options="mail_providers" placeholder="服务商品牌"
              size="medium" @update:value="handleUpdateMailProviders"
            />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>选择服务商品牌，系统将代填服务地址</p>
              </div>
            </template>
          </n-form-item>
          <n-form-item path="mail_host" label="服务地址">
            <n-input
              v-model:value="model.mail_host" size="medium" round placeholder="imap服务器地址:端口/协议/加密"
              :disabled="mail_host_disabled"
            />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>例如 imap.qq.com:993/imap/ssl，如不确定，请选择你的邮箱服务类型，由系统自动填写</p>
              </div>
            </template>
          </n-form-item>
          <n-form-item path="mail_username" label="邮箱账号" class="mt-3">
            <n-input v-model:value="model.mail_username" size="medium" round placeholder="对应的邮箱地址" />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>请输入完整的邮箱地址，例如 yourname@qq.com</p>
              </div>
            </template>
          </n-form-item>
          <n-form-item path="mail_password" label="密码或授权码" class="mt-3">
            <n-input v-model:value="model.mail_password" size="medium" round placeholder="密码或者授权码" />
            <template #feedback>
              <div style="font-size: 12px; color: #888;">
                <p>确保已开启 IMAP/SMTP 服务，并使用“授权码”作为密码登录。</p>
                <p>如使用 QQ、163、Gmail 等邮箱，请前往邮箱【设置 → 账户 → 开启服务 → 获取授权码】中开启 IMAP 并生成授权码</p>
                <p>授权码不同于登录密码。建议使用常用邮箱并保持可用状态。</p>
                <p>
                  以QQ邮箱为例，授权码获取方式可以参考：<a
                    href="https://wx.mail.qq.com/list/readtemplate?name=app_intro.html#/agreement/authorizationCode"
                  >官方文档</a>
                </p>
              </div>
            </template>
          </n-form-item>
          <n-button
            round strong secondary type="primary" class="mt-3 w-100%" :loading="loading"
            @click="handleSave"
          >
            保存
          </n-button>
        </n-form>
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { defineProps, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { request } from '@/utils/http/request'

const props = defineProps({
  data: Object,
})

const router = useRouter()

// 下拉列表
const mail_providers = ref([])

// 表单参数
const loading = ref(false)
const formRef = ref()
const model = ref({
  mail_providers: null,
  mail_host: '',
  mail_username: '',
  mail_password: '',
})
const mail_host_disabled = ref(false)
const rules = ref({
  mail_host: {
    required: true,
    validator(rule, value) {
      if (!value) {
        return new Error('服务地址不可以为空')
      }
      return true
    },
  },
  mail_username: {
    required: true,
    validator(rule, value) {
      if (!value) {
        return new Error('邮箱账号不可以为空')
      }
      return true
    },
  },
  mail_password: {
    required: true,
    validator(rule, value) {
      if (!value) {
        return new Error('密码或授权码不可以为空')
      }
      return true
    },
  },
})

// 处理父组件传值
watch(
  () => props.data,
  (val) => {
    if (val && Object.keys(val).length > 0) {
      mail_providers.value = val.mail_providers
      model.value.mail_host = val.mail_host
      model.value.mail_username = val.mail_username
      model.value.mail_password = val.mail_password
      if (val.mail_host) {
        val.mail_providers.forEach((item) => {
          if (item.value === val.mail_host) {
            model.value.mail_providers = val.mail_host
            mail_host_disabled.value = true
          }
        })
      }
    }
  },
  { immediate: true },
)

function handleUpdateMailProviders(value) {
  model.value.mail_host = value
  if (value === '') {
    mail_host_disabled.value = false
  }
  else {
    mail_host_disabled.value = true
  }
}

async function handleSave() {
  await formRef.value?.validate((errors) => {
    if (errors) {
      errors.forEach((_errors) => {
        _errors.forEach((item) => {
          window.$message?.error(item.message)
        })
      })
      return false
    }
  })
  loading.value = true
  request.post('/auth/set-billmail-config', {
    mail_host: model.value.mail_host,
    mail_username: model.value.mail_username,
    mail_password: model.value.mail_password,
  }).then((res) => {
    loading.value = false
    window.$message?.success('保存成功')
    if (res.data.login) {
      router.push('/login')
    }
  })
}
</script>
