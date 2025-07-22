<template>
  <n-drawer v-model:show="visible" placement="right" width="400" @update:show="handleUpdateShow">
    <n-drawer-content>
      <template #header>
        <div class="w-full flex items-center justify-center">
          <div class="flex-1 text-18px font-500">
            {{ title }}
          </div>
          <n-button
            v-if="props.dataId && !completed" strong secondary round type="success" size="tiny"
            @click="completeTodo(true, props.dataId)"
          >
            标记完成
          </n-button>
          <n-button
            v-if="props.dataId && completed" strong secondary round type="error" size="tiny"
            @click="completeTodo(false, props.dataId)"
          >
            取消完成
          </n-button>
        </div>
      </template>

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
            <n-date-picker v-if="date_type === 'date_range'" v-model:value="date_range" type="daterange" clearable />
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
  defaultTimestamp: { type: Number, default: null },
})
const emit = defineEmits(['update:modelValue'])

const visible = ref(props.modelValue)
const loading = ref(false)
const title = ref('Todo 详情')
const completed = ref(false)
const isDark = window.$isDark

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

const rules = ref({
  title: [{ required: true, message: '标题不能为空', trigger: 'blur' }],
})

const category = ref([])
const colors = ref([])
const repeat_type_options = ref([])
const date_type = ref('single_date')
const date_type_options = ref([
  { value: 'single_date', label: '单个日期' },
  { value: 'date_range', label: '日期区间' },
])
const date_range = ref([null, null])

function handleUpdateShow(val) {
  visible.value = val
  emit('update:modelValue', val)
}

async function saveTodo() {
  try {
    await formRef.value?.validate()

    form.value.start_at = date_range.value[0]
    form.value.end_at = form.value.start_at
    if (date_type.value === 'date_range') {
      form.value.end_at = date_range.value[1]
    }

    if (!form.value.start_at || !form.value.end_at) {
      window.$message.error('请填写日期')
      return
    }

    form.value.start_at = normalizeDate(form.value.start_at, true)
    form.value.end_at = normalizeDate(form.value.end_at, false)

    form.value.repeat_until = form.value.repeat_until
      ? normalizeDate(form.value.repeat_until, false)
      : null

    request.post('/todo/save-todo', {
      todo_id: props.dataId,
      ...form.value,
    }).then(({ code }) => {
      if (code === 0) {
        window.$message.success('修改成功')
        handleUpdateShow(false)
      }
    })
  }
  catch (e) {
    console.warn('表单验证失败:', e)
    // 验证未通过
  }
}

function normalizeDate(timestamp, isStart) {
  const d = new Date(timestamp)
  return new Date(
    d.getFullYear(),
    d.getMonth(),
    d.getDate(),
    isStart ? 0 : 23,
    isStart ? 0 : 59,
    isStart ? 0 : 59,
    isStart ? 0 : 999,
  ).getTime()
}

function completeTodo(complete, id) {
  const url = complete ? '/todo/complete-todo' : '/todo/uncomplete-todo'
  request.post(url, { todo_id: id }).then(({ code }) => {
    if (code === 0) {
      window.$message.success('操作成功')
      handleUpdateShow(false)
    }
  })
}

function loadTodoDetail(id) {
  loading.value = true
  resetForm()

  request.post('/todo/get-todo', { todo_id: id }).then(({ code, data }) => {
    if (code !== 0)
      return

    category.value = [{ value: null, label: '无分组' }, ...(data?.categories || []).map(i => ({ value: i.key, label: i.value }))]
    colors.value = data?.color?.map(i => ({ value: i.key, label: i.color })) || []
    repeat_type_options.value = data?.repeat_type?.map(i => ({ value: i.key, label: i.value })) || []

    if (data?.data) {
      completed.value = data.data.completed
      Object.assign(form.value, {
        category_id: data.data.category_id,
        title: data.data.title,
        content: data.data.content,
        color: data.data.color,
        start_at: data.data.start_at * 1000,
        end_at: data.data.end_at * 1000,
        repeat_type: data.data.repeat_type,
        repeat_interval: data.data.repeat_interval,
        repeat_until: data.data.repeat_until ? data.data.repeat_until * 1000 : null,
      })
      date_range.value = [form.value.start_at, form.value.end_at]
      date_type.value = (form.value.end_at - form.value.start_at) > 86400 * 1000 ? 'date_range' : 'single_date'
    }
    else if (props.defaultTimestamp != null) {
      form.value.start_at = props.defaultTimestamp
      form.value.end_at = props.defaultTimestamp
      date_range.value = [props.defaultTimestamp, props.defaultTimestamp]
      date_type.value = 'single_date'
    }

    loading.value = false
  })
}

function resetForm() {
  form.value = {
    category_id: null,
    title: '',
    content: '',
    color: '',
    start_at: null,
    end_at: null,
    repeat_type: null,
    repeat_interval: null,
    repeat_until: null,
  }
}

watch(() => props.modelValue, (val) => {
  visible.value = val
})

watch([visible, () => props.dataId], ([show, id]) => {
  if (show) {
    loadTodoDetail(id)
  }
})
</script>
