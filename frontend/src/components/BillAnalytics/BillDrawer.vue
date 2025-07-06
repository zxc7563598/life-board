<template>
  <n-drawer v-model:show="internalShow" :width="530" placement="right" @after-leave="reset">
    <n-drawer-content>
      <template #header>
        数据列表
      </template>
      <n-spin v-if="loading" size="small" class="h-100% w-100%" />
      <n-list clickable hoverable>
        <n-list-item v-for="(item, index) in listData" :key="index">
          <n-thing :title="item.counterparty" content-style="margin-top: 10px;">
            <template #description>
              <n-space size="small" style="margin-top: 4px">
                <NTag
                  v-if="item.platform === 0" :bordered="false" size="small"
                  :color="tagColor.wechat[isDark ? 'dark' : 'light']"
                >
                  <template #icon>
                    <i class="i-tabler-brand-wechat" />
                  </template>
                  微信
                </NTag>
                <NTag
                  v-if="item.platform === 1" :bordered="false" type="info" size="small"
                  :color="tagColor.alipay[isDark ? 'dark' : 'light']"
                >
                  <template #icon>
                    <i class="i-tabler-brand-alipay" />
                  </template>
                  支付宝
                </NTag>
                <NTag :bordered="false" type="info" size="small">
                  {{ item.trade_type }}
                </NTag>
                <NTag
                  v-if="item.income_type === 1" :bordered="false" size="small"
                  :color="tagColor.income[isDark ? 'dark' : 'light']"
                >
                  收入
                </NTag>
                <NTag
                  v-if="item.income_type === 2" :bordered="false" size="small"
                  :color="tagColor.expense[isDark ? 'dark' : 'light']"
                >
                  支出
                </NTag>
                <NTag
                  v-if="item.income_type === 0" :bordered="false" size="small"
                  :color="tagColor.uncategorized[isDark ? 'dark' : 'light']"
                >
                  不记收支
                </NTag>
              </n-space>
            </template>
            订单号：{{ item.trade_no }}<br>
            交易方式：{{ item.payment_method }}<br>
            支付状态：{{ item.trade_status }}<br>
            产品说明：{{ item.product_name }}<br>
            <NTag :bordered="false" size="small" :color="tagColor.time[isDark ? 'dark' : 'light']">
              {{ item.trade_time }}
            </NTag>
          </n-thing>
          <template #suffix>
            <div v-if="item.income_type === 0" class="w-100px text-1rem">
              {{ item.amount }}
            </div>
            <div v-if="item.income_type === 1" class="w-100px text-1rem text-red">
              + {{ item.amount }}
            </div>
            <div v-if="item.income_type === 2" class="w-100px text-1rem text-green">
              - {{ item.amount }}
            </div>
          </template>
        </n-list-item>
      </n-list>
    </n-drawer-content>
  </n-drawer>
</template>

<script setup>
import { defineEmits, defineProps, ref, watch } from 'vue'
import { request } from '@/utils/http/request'

const props = defineProps({
  show: Boolean,
  query: Object, // 查询参数
})

const emit = defineEmits(['update:show'])

const internalShow = ref(false)
const listData = ref([])
const loading = ref(false)

const isDark = window.$isDark
const tagColor = ref({
  alipay: {
    light: {
      color: '#E6F0FF',
      borderColor: '#1677FF',
      textColor: '#1677FF',
    },
    dark: {
      color: '#1A3A7F',
      borderColor: '#4A90E2',
      textColor: '#A8D1FF',
    },
  },
  wechat: {
    light: {
      color: '#E6F8F0',
      borderColor: '#07C160',
      textColor: '#07C160',
    },
    dark: {
      color: '#1A4A3A',
      borderColor: '#2E8B57',
      textColor: '#A8F5CC',
    },
  },
  income: {
    light: {
      color: '#FFF1F0',
      borderColor: '#F5222D',
      textColor: '#F5222D',
    },
    dark: {
      color: '#4A1A1A',
      borderColor: '#E84749',
      textColor: '#FFA8A8',
    },
  },
  expense: {
    light: {
      color: '#E6F7F0',
      borderColor: '#52C41A',
      textColor: '#52C41A',
    },
    dark: {
      color: '#1A4A2A',
      borderColor: '#2E8B57',
      textColor: '#A8F5A8',
    },
  },
  uncategorized: {
    light: {
      color: '#FAFAFA',
      borderColor: '#D9D9D9',
      textColor: '#666666',
    },
    dark: {
      color: '#333333',
      borderColor: '#555555',
      textColor: '#AAAAAA',
    },
  },
  time: {
    light: {
      color: '#F6F6F6',
      borderColor: '#D4D4D4',
      textColor: '#666666',
    },
    dark: {
      color: '#252525',
      borderColor: '#444444',
      textColor: '#CCCCCC',
    },
  },
})

// 控制显示
watch(() => props.show, (val) => {
  internalShow.value = val
})

// 绑定回父组件
watch(internalShow, (val) => {
  emit('update:show', val)
})

// 监听查询参数 + 抽屉打开
watch(
  () => [props.query, internalShow.value],
  ([query, show]) => {
    if (show && query) {
      fetchData(query)
    }
  },
  { immediate: true, deep: true },
)

async function fetchData(query) {
  loading.value = true
  // 获取数据接口
  const { code, data } = await request.post('/bill/get-bill-list-all', {
    income_type: query.income_type,
    trade_type: query.trade_type,
    trade_time: query.trade_time,
  })
  if (code === 0) {
    listData.value.push(...data.list_data)
  }
  loading.value = false
}

function reset() {
  listData.value = []
}
</script>
