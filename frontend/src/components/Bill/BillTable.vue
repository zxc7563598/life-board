<template>
  <div class="opacity-0 opacity-100 duration-1500">
    <n-card
      class="min-w-280px w-full border border-gray-200 rounded-xl opacity-0 shadow-lg transition duration-1500 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
      :class="{ 'opacity-100': showSearchInit }"
    >
      <div>
        <n-form
          ref="searchFormRef" label-placement="top" :label-width="80" :model="searchForm" size="medium"
          class="mb-3 flex flex-wrap gap-4 text-base font-medium" :show-feedback="false"
        >
          <n-form-item label="交易日期" path="trade_time" class="min-w-230px flex-1">
            <n-date-picker
              v-model:value="searchForm.trade_time" type="daterange" clearable
              class="w-full"
            />
          </n-form-item>
          <n-form-item label="交易平台" path="platform" class="min-w-150px flex-1">
            <n-select
              v-model:value="searchForm.platform" placeholder="请选择交易平台" class="w-full"
              label-field="value" value-field="key" :options="searchEnums.platform"
            />
          </n-form-item>

          <n-form-item label="收支类型" path="income_type" class="min-w-150px flex-1">
            <n-select
              v-model:value="searchForm.income_type" placeholder="请选择收支类型" class="w-full"
              label-field="value" value-field="key" :options="searchEnums.income_type"
            />
          </n-form-item>
          <n-form-item label="交易单号" path="trade_no" class="min-w-230px flex-1">
            <n-input v-model:value="searchForm.trade_no" class="w-full" placeholder="请输入交易单号" />
          </n-form-item>
          <n-form-item label="交易类型" path="income_type" class="w-100%">
            <n-space>
              <NTag
                v-for="(item, index) in searchEnums.trade_type" :key="index"
                v-model:checked="tradeTypeCheck[index]" checkable
              >
                {{ item }}
              </NTag>
            </n-space>
          </n-form-item>
          <div class="w-full flex flex-row">
            <div class="mr-4 flex-1">
              <NButton
                strong secondary round type="primary" class="w-full"
                @click="fetchSearchResults(false)"
              >
                搜索
              </NButton>
            </div>
            <div class="flex-1">
              <NButton strong secondary round class="w-full" @click="resetSearchAndFetch">
                重置
              </NButton>
            </div>
          </div>
        </n-form>
      </div>
    </n-card>
    <n-card
      class="mt-4 w-full border border-gray-200 rounded-xl opacity-0 shadow-lg transition duration-1500 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
      :class="{ 'opacity-100': showTableInit }"
    >
      <div>
        <div class="mb-3 text-base font-medium">
          <n-list clickable hoverable>
            <template #footer>
              <NButton round tertiary type="info" class="w-full" :loading="loading" @click="nextPage">
                加载更多 {{ tableData.length }} / {{ pagination.itemCount }}
              </NButton>
            </template>
            <n-list-item v-for="(item, index) in tableData" :key="index">
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
        </div>
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { NButton, NTag } from 'naive-ui'
import { defineProps, onMounted, ref, watch } from 'vue'
import { request } from '@/utils/http/request'

const props = defineProps({
  searchEnums: Object,
})

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

onMounted(async () => {
  fetchSearchResults(false)
})

const showSearchInit = ref(false)
const showTableInit = ref(false)
const searchEnums = ref({})
const tradeTypeCheck = ref([])

const searchFormRef = ref()
const searchForm = ref({
  trade_no: '',
  platform: null,
  income_type: null,
  trade_time: null,
  trade_type: [],
})

// 处理父组件传值
watch(
  () => props.searchEnums,
  (val) => {
    if (val && Object.keys(val).length > 0) {
      searchEnums.value = val
      searchEnums.value.trade_type.forEach((item, key) => {
        tradeTypeCheck.value[key] = false
      })
      showSearchInit.value = true
    }
  },
  { immediate: true },
)

const loading = ref(true)
const tableData = ref([])
const pagination = ref({
  pageNo: 1,
  pageSize: 20,
  itemCount: 10,
})

// 清空搜索信息
async function resetSearchAndFetch() {
  searchForm.value = {
    trade_no: '',
    platform: null,
    income_type: null,
    trade_time: null,
    trade_type: [],
  }
  tradeTypeCheck.value.forEach((item, key) => {
    tradeTypeCheck.value[key] = false
  })
  fetchSearchResults(false)
}

// 获取列表信息
async function fetchSearchResults(append = false) {
  loading.value = true
  if (!append) {
    pagination.value.pageNo = 1
  }
  searchForm.value.trade_type = []
  tradeTypeCheck.value.forEach((item, key) => {
    if (item) {
      searchForm.value.trade_type.push(searchEnums.value.trade_type[key])
    }
  })
  const { code, data } = await request.post('/bill/get-bill-list', {
    pageNo: pagination.value.pageNo,
    pageSize: pagination.value.pageSize,
    trade_no: searchForm.value.trade_no,
    platform: searchForm.value.platform,
    income_type: searchForm.value.income_type,
    trade_type: searchForm.value.trade_type,
    trade_time: searchForm.value.trade_time,
  })
  if (code === 0) {
    showTableInit.value = true
    if (append) {
      tableData.value.push(...data.pageData)
    }
    else {
      tableData.value = []
      tableData.value.push(...data.pageData)
    }
    pagination.value.itemCount = data.total
  }
  loading.value = false
}

// 加载下一页
function nextPage() {
  pagination.value.pageNo += 1
  fetchSearchResults(true)
}
</script>
