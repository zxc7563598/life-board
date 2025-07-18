<template>
  <n-drawer v-model:show="visible" placement="right" width="400" @update:show="handleUpdateShow">
    <n-drawer-content :title="title">
      <div v-if="loading">
        加载中...
      </div>
      <div v-else>
        <n-form ref="formRef" :model="form" :rules="rules">
          <n-form-item path="category" label="分组">
            <n-select v-model:value="form.category_id" :options="category" placeholder="无分组" />
          </n-form-item>
          <n-form-item path="title" label="标题">
            <n-input v-model:value="form.title" />
          </n-form-item>
          <n-form-item path="content" label="内容描述">
            <n-input v-model:value="form.content" type="textarea" />
          </n-form-item>
          <n-form-item path="color" label="颜色">
            <n-radio-group v-model:value="form.color" name="radiogroup">
              <n-space>
                <n-radio v-for="color in colors" :key="color.value" :value="color.value">
                  <div
                    class="h-6 min-h-6 min-w-6 w-6"
                    :style="{ background: isDark ? color.label.dark : color.label.light }"
                  />
                </n-radio>
              </n-space>
            </n-radio-group>
          </n-form-item>
          <n-form-item path="date_type" label="日期类型">
            <n-select v-model:value="date_type" :options="date_type_options" />
          </n-form-item>
          <n-form-item path="date" label="日期">
            <n-date-picker
              v-if="date_type === 'single_date'" v-model:value="date_range[0]" type="date"
              class="w-full"
            />
            <n-date-picker
              v-if="date_type === 'date_range'" v-model:value="date_range" type="daterange"
              clearable
            />
          </n-form-item>
          <n-form-item path="repeat_type" label="重复类型">
            <n-select v-model:value="form.repeat_type" :options="repeat_type_options" />
          </n-form-item>
          <n-form-item v-if="form.repeat_type > 0" path="repeat_interval" label="重复间隔">
            <n-input v-model:value="form.repeat_interval">
              <template #suffix>
                {{ repeat_type_options.find(option => option.value === form.repeat_type)?.label || '' }}
              </template>
            </n-input>
          </n-form-item>
          <n-form-item v-if="form.repeat_type > 0" path="repeat_until" label="重复截止时间">
            <n-date-picker v-model:value="form.repeat_until" type="date" class="w-full" />
          </n-form-item>
          <n-button strong secondary type="info" class="w-full" @click="saveTodo">
            保存
          </n-button>
        </n-form>
      </div>
    </n-drawer-content>
  </n-drawer>
</template>

<script setup>
import { ref, watch } from 'vue'
import { request } from '@/utils/http/request'

const props = defineProps({
  modelValue: Boolean,
  dataId: [String, Number],
})

const emit = defineEmits(['update:modelValue'])

const isDark = window.$isDark

const visible = ref(props.modelValue)
const loading = ref(false)
const title = ref('Todo 详情')

// 抽屉关闭后通知父组件
function handleUpdateShow(val) {
  visible.value = val
  emit('update:modelValue', val)
}

// 表单数据
const formRef = ref(null)
const form = ref({
  category_id: null,
  title: '',
  content: '',
  color: '',
  start_at: null,
  end_at: null,
  repeat_type: null,
  repeat_interval: null,
  repeat_until: null,
})
const category = ref([])
const rules = ref({})
const categories_options = ref([])
const colors = ref([])
const date_type = ref('single_date')
const date_type_options = ref([
  { value: 'single_date', label: '单个日期' },
  { value: 'date_range', label: '日期区间' },
])
const date_range = ref([null, null])
const repeat_type_options = ref([])

function saveTodo() {
  const now = new Date()
  form.value.start_at = date_range.value[0]
  form.value.end_at = date_range.value[1]
  // 处理 start_at
  if (!form.value.start_at) {
    const todayStart = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime()
    form.value.start_at = todayStart
  }
  else {
    const d = new Date(form.value.start_at)
    const dateStart = new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime()
    form.value.start_at = dateStart
  }
  // 处理 end_at
  if (!form.value.end_at) {
    const todayEnd = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999).getTime()
    form.value.end_at = todayEnd
  }
  else {
    const d = new Date(form.value.end_at)
    const dateEnd = new Date(d.getFullYear(), d.getMonth(), d.getDate(), 23, 59, 59, 999).getTime()
    form.value.end_at = dateEnd
  }
  // 处理 repeat_until
  if (!form.value.repeat_until) {
    form.value.repeat_until = null
  }
  else {
    const d = new Date(form.value.repeat_until)
    const dateEnd = new Date(d.getFullYear(), d.getMonth(), d.getDate(), 23, 59, 59, 999).getTime()
    form.value.repeat_until = dateEnd
  }
  request.post('/todo/save-todo', {
    todo_id: props.dataId,
    category_id: form.value.category_id,
    title: form.value.title,
    content: form.value.content,
    color: form.value.color,
    start_at: form.value.start_at,
    end_at: form.value.end_at,
    repeat_type: form.value.repeat_type,
    repeat_interval: form.value.repeat_interval,
    repeat_until: form.value.repeat_until,

  }).then(({ code }) => {
    if (code === 0) {
      window.$message.success('修改成功')
      handleUpdateShow(false)
    }
  })
}

// 同步外部 v-model 状态
watch(() => props.modelValue, (val) => {
  visible.value = val
})

// 监听 visible 和 dataId，拉数据
watch([visible, () => props.dataId], async ([show, id]) => {
  if (show && id != null) {
    loading.value = true
    // 获取数据
    request.post('/todo/get-todo', { todo_id: id }).then(({ code, data }) => {
      colors.value = []
      category.value = []
      repeat_type_options.value = []
      date_range.value = []
      if (code === 0) {
        category.value.push({
          value: null,
          label: '无分组',
        })
        data.categories.forEach((item) => {
          category.value.push({
            value: item.key,
            label: item.value,
          })
        })
        data.color.forEach((item) => {
          colors.value.push({
            value: item.key,
            label: item.color,
          })
        })
        data.repeat_type.forEach((item) => {
          repeat_type_options.value.push({
            value: item.key,
            label: item.value,
          })
        })
        data.categories.forEach((item) => {
          categories_options.value.push({
            value: item.key,
            label: item.value,
          })
        })
        form.value.category_id = data.data.category_id
        form.value.title = data.data.title
        form.value.content = data.data.content
        form.value.color = data.data.color
        form.value.start_at = data.data.start_at * 1000
        form.value.end_at = data.data.end_at * 1000
        form.value.repeat_type = data.data.repeat_type
        form.value.repeat_interval = data.data.repeat_interval
        form.value.repeat_until = data.data.repeat_until ? data.data.repeat_until * 1000 : null
        date_range.value = [form.value.start_at, form.value.end_at]
        loading.value = false
        if ((data.data.end_at - data.data.start_at) > 86400) {
          date_type.value = 'date_range'
        }
        else {
          date_type.value = 'single_date'
        }
      }
    })
  }
})
</script>
