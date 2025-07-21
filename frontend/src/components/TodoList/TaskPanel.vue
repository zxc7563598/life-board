<template>
  <div class="h-full min-w-800px flex flex-1 flex-wrap gap-4">
    <div class="h-full max-w-300px min-w-300px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
      <n-card
        content-class="px-0!"
        class="h-full w-full border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
      >
        <n-menu
          :indent="18" :collapsed-width="64" :collapsed-icon-size="22" :options="menuOptions" :value="selectedKey"
          @update:value="getTodoLists"
        />
      </n-card>
    </div>
    <div class="h-full flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
      <n-card
        class="h-full w-auto overflow-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
      >
        <n-spin :show="todoLoading">
          <NInput
            v-model:value="addTodoTitle" size="medium" round placeholder="输入内按下 ↵ 回车键快速创建"
            @keydown="handleCreateTodo" @blur="createTodo"
          />
          <n-divider />
          <n-collapse :default-expanded-names="['1', '2']">
            <n-collapse-item name="1">
              <template #header>
                <div class="font-bold">
                  未完成
                </div>
              </template>
              <n-card
                v-for="(item, index) in todo_incomplete" :key="index"
                class="mb-2 h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
              >
                <div class="flex items-center">
                  <div class="flex flex-1 flex-row items-center">
                    <n-checkbox :checked="item.completed" @update:checked="val => completeTodo(val, item.id)" />
                    <div
                      class="ml-1 mr-1 h-3 min-h-3 min-w-3 w-3"
                      :style="{ background: isDark ? todoColor[item.color].dark : todoColor[item.color].light }"
                    />
                    <span
                      class="truncate-label flex-1 cursor-pointer text-gray-800 transition-colors duration-200 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400"
                      @click="openDrawer(item.id)"
                    >
                      {{ item.title }}
                    </span>
                    <span v-if="item.repeat_type > 0" class="ml-a pl-1">
                      <i class="i-tabler-refresh block text-sm text-gray-400 dark:text-gray-500" />
                    </span>
                    <span class="ml-a pl-1 text-xs text-gray-600 dark:text-gray-400">{{ item.date }}</span>
                  </div>
                  <div class="ml-2">
                    <i
                      class="i-tabler-trash-x block cursor-pointer text-sm text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400"
                      @click="handleDeletedTodo(item.id)"
                    />
                  </div>
                </div>
              </n-card>
            </n-collapse-item>
            <n-collapse-item name="2">
              <template #header>
                <div class="font-bold">
                  已完成
                </div>
              </template>
              <n-card
                v-for="(item, index) in todo_complete" :key="index"
                class="mb-2 h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
              >
                <div class="flex items-center">
                  <div class="flex flex-1 flex-row items-center">
                    <n-checkbox :checked="item.completed" @update:checked="val => completeTodo(val, item.id)" />
                    <div
                      class="ml-1 mr-1 h-3 min-h-3 min-w-3 w-3"
                      :style="{ background: isDark ? todoColor[item.color].dark : todoColor[item.color].light }"
                    />
                    <span
                      class="truncate-label flex-1 cursor-pointer text-gray-800 transition-colors duration-200 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400"
                    >
                      {{ item.title }}
                    </span>
                    <span v-if="item.repeat_type > 0" class="ml-a pl-1">
                      <i class="i-tabler-refresh block text-sm text-gray-400 dark:text-gray-500" />
                    </span>
                    <span class="ml-a pl-1 text-xs text-gray-600 dark:text-gray-400">{{ item.date }}</span>
                  </div>
                  <div class="ml-2">
                    <i
                      class="i-tabler-trash-x block cursor-pointer text-sm text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400"
                      @click="handleDeletedTodo(item.id)"
                    />
                  </div>
                </div>
              </n-card>
            </n-collapse-item>
          </n-collapse>
        </n-spin>
      </n-card>
    </div>
  </div>
  <TodoDetailDrawer v-model="drawerVisible" :data-id="currentId" />
</template>

<script setup>
import { NDropdown, NEllipsis, NInput, useDialog } from 'naive-ui'
import { h, nextTick, onMounted, ref, watch } from 'vue'
import TodoDetailDrawer from '@/components/TodoList/TodoDetailDrawer.vue'
import { request } from '@/utils/http/request'

const isDark = window.$isDark
const dataInit = ref(true)
const todoLoading = ref(false)

// 分类列表
const editingInputRef = ref(null)
const editingId = ref(null)
const editingValue = ref('')
const addingValue = ref('')
const menuOptions = ref([])
const selectedKey = ref('today')
const defaulMenuOptions = [
  {
    label: '今天',
    key: 'today',
    icon: () => h('i', { class: 'i-tabler-browser-check' }),
  },
  {
    label: '最近7天',
    key: 'thisWeek',
    icon: () => h('i', { class: 'i-tabler-calendar-week-filled' }),
  },
  {
    label: '全部待办',
    key: 'all',
    icon: () => h('i', { class: 'i-tabler-calendar-week' }),
  },
  {
    key: 'divider',
    type: 'divider',
  },
  {
    label: () =>
      h(NInput, {
        'placeholder': '输入内按下 ↵ 回车键创建分类',
        'value': addingValue.value,
        'onUpdate:value': v => addingValue.value = v,
        'onKeydown': (e) => {
          if (e.key === 'Enter') {
            if (addingValue.value) {
              addingValue.value = ''
              request.post('/todo/update-todo-category', { name: e.target.value }).then(({ code }) => {
                if (code === 0) {
                  window.$message.success('添加成功')
                  getTodoCategories()
                }
              })
            }
          }
        },
        'clearable': false,
      }),
    key: 'markInput',
  },
]

// 获取分类
const labelMenuOptions = ref([])
function getTodoCategories() {
  request.post('/todo/get-todo-categories').then(({ code, data }) => {
    if (code === 0) {
      labelMenuOptions.value = []
      data.todo_categories.forEach((item) => {
        labelMenuOptions.value.push({
          label: () =>
            h(
              'div',
              { class: 'flex items-center justify-between w-full' },
              editingId.value === item.id
                ? [
                    h(NInput, {
                      'ref': editingInputRef,
                      'value': editingValue.value,
                      'autofocus': true,
                      'style': { maxWidth: 'calc(100%)' },
                      'onUpdate:value': v => editingValue.value = v,
                      'onBlur': () => handleCategorySave(item),
                      'onKeydown': (e) => {
                        if (e.key === 'Enter')
                          handleCategorySave(item)
                      },
                    }),
                  ]
                : [
                    h(NEllipsis, {
                      style: { maxWidth: 'calc(100% - 1.5rem)' },
                    }, {
                      default: () => item.name,
                    }),
                    h(
                      NDropdown,
                      {
                        trigger: 'click',
                        options: [
                          { label: '编辑', key: 'edit' },
                          { label: '删除', key: 'delete' },
                        ],
                        onSelect: key => handleCategoriesAction(key, item),
                      },
                      {
                        default: () =>
                          h('i', {
                            class: 'i-tabler-dots-vertical cursor-pointer text-gray-400 hover:text-gray-600',
                          }),
                      },
                    ),
                  ],
            ),
          key: item.id,
          icon: () => h('i', { class: 'i-tabler-tag-starred' }),
        })
      })
      menuOptions.value = [...defaulMenuOptions, ...labelMenuOptions.value]
    }
  })
}

// 分类点击编辑或删除
const dialog = useDialog()
function handleCategoriesAction(key, item) {
  if (key === 'edit') {
    // 编辑逻辑
    editingId.value = item.id
    editingValue.value = item.name
    nextTick(() => {
      editingInputRef.value?.focus()
    })
  }
  if (key === 'delete') {
    // 删除逻辑
    dialog.warning({
      title: '警告',
      content: '分组删除后无法恢复，分组内的内容不会删除，而是会变更为无分组的内容，你确定要删除分组吗？',
      positiveText: '确定',
      negativeText: '不确定',
      draggable: true,
      onPositiveClick: () => {
        const index = labelMenuOptions.value.findIndex(option => option.id === item.id)
        if (index !== -1) {
          labelMenuOptions.value.splice(index, 1)
        }
        request.post('/todo/delete-todo-category', { category_id: item.id }).then(({ code }) => {
          if (code === 0) {
            window.$message.success('删除成功')
            getTodoCategories()
            selectedKey.value = 'today'
            getTodoLists('today')
          }
        })
      },
    })
  }
}

// 执行保存逻辑
function handleCategorySave(item) {
  editingId.value = null
  if (item.name !== editingValue.value) {
    item.name = editingValue.value
    request.post('/todo/update-todo-category', { category_id: item.id, name: editingValue.value }).then(({ code }) => {
      if (code === 0) {
        window.$message.success('修改成功')
        getTodoCategories()
      }
    })
  }
}

// 获取某个分类的待办清单
const todoColor = ref([])
const todo_complete = ref([])
const todo_incomplete = ref([])
function getTodoLists(key) {
  if (key === 'divider' || key === 'markInput') {
    return false
  }
  todoLoading.value = true
  selectedKey.value = key
  const category_id = ref(null)
  const end_at = ref(null)
  const endOfToday = new Date()
  switch (key) {
    case 'all':

      break
    case 'today':
      endOfToday.setHours(23, 59, 59, 0)
      end_at.value = endOfToday.getTime()
      break
    case 'thisWeek':
      endOfToday.setDate(endOfToday.getDate() + 7)
      endOfToday.setHours(23, 59, 59, 0)
      end_at.value = endOfToday.getTime()
      break
    default:
      category_id.value = key
      break
  }
  // 获取数据
  request.post('/todo/list-todos', { category_id: category_id.value, end_at: end_at.value }).then(({ code, data }) => {
    if (code === 0) {
      todo_complete.value = []
      todo_incomplete.value = []
      data.color.forEach((item) => {
        todoColor.value[item.key] = item.color
      })
      data.data.forEach((item) => {
        if (item.completed) {
          todo_complete.value.push({
            id: item.id,
            date: item.date,
            title: item.title,
            completed: item.completed,
            color: item.color,
            repeat_type: item.repeat_type,
            repeat_interval: item.repeat_interval,
            repeat_until: item.repeat_until,
          })
        }
        else {
          todo_incomplete.value.push({
            id: item.id,
            date: item.date,
            title: item.title,
            completed: item.completed,
            color: item.color,
            repeat_type: item.repeat_type,
            repeat_interval: item.repeat_interval,
            repeat_until: item.repeat_until,
          })
        }
      })
    }
  }).finally(() => {
    todoLoading.value = false
  })
}

// 操作todo完成
function completeTodo(val, id) {
  todoLoading.value = true
  if (val) {
    request.post('/todo/complete-todo', { todo_id: id }).then(({ code }) => {
      if (code === 0) {
        getTodoLists(selectedKey.value)
        window.$message.success('操作成功')
      }
    }).finally(() => {
      todoLoading.value = false
    })
  }
  else {
    request.post('/todo/uncomplete-todo', { todo_id: id }).then(({ code }) => {
      if (code === 0) {
        getTodoLists(selectedKey.value)
        window.$message.success('操作成功')
      }
    }).finally(() => {
      todoLoading.value = false
    })
  }
}

// 按回车创建todo
const addTodoTitle = ref('')
function handleCreateTodo(e) {
  if (e.key === 'Enter') {
    createTodo()
  }
}

// 添加todo
function createTodo() {
  if (addTodoTitle.value) {
    todoLoading.value = true
    let category_id = null
    if (!['today', 'thisWeek', 'all', 'divider', 'markInput'].includes(selectedKey.value)) {
      category_id = selectedKey.value
    }
    request.post('/todo/save-todo', {
      title: addTodoTitle.value,
      category_id,
    }).then(({ code }) => {
      if (code === 0) {
        addTodoTitle.value = ''
        getTodoLists(selectedKey.value)
        window.$message.success('添加成功')
      }
    }).finally(() => {
      todoLoading.value = false
    })
  }
}

// 删除todo
function handleDeletedTodo(id) {
  dialog.warning({
    title: '警告',
    content: '删除的记录无法恢复，你确定要删除吗？',
    positiveText: '确定',
    negativeText: '不确定',
    draggable: true,
    onPositiveClick: () => {
      todoLoading.value = true
      request.post('/todo/delete-todo', { todo_id: id }).then(({ code }) => {
        if (code === 0) {
          getTodoLists(selectedKey.value)
          window.$message.success('删除成功')
        }
      }).finally(() => {
        todoLoading.value = false
      })
    },
  })
}

// 细节弹窗
const drawerVisible = ref(false)
const currentId = ref(null)
function openDrawer(id) {
  currentId.value = id
  drawerVisible.value = true
}
watch(drawerVisible, (val) => {
  if (val === false) {
    getTodoLists(selectedKey.value)
  }
})

// 内容
onMounted(async () => {
  getTodoCategories()
  getTodoLists('today')
})
</script>

<style scoped>
.truncate-label {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

:deep(.task-title .n-input-wrapper) {
  padding-left: 0 !important;
  padding-right: 0 !important;
}

:deep(.w-full .n-checkbox__label) {
  width: 100%;
}
</style>
