<!-- 收入分类Top10 -->
<template>
  <div class="min-h-320px min-w-200px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div>
        <div class="mb-3 text-base font-medium">
          <VChart :option="pieOption" autoresize class="min-h-320px min-w-200px flex-1" @click="onChartClick" />
        </div>
        <Transition name="fade-slide" mode="out-in">
          <div v-if="showBar" key="bar" class="mb-3 text-base font-medium">
            <VChart
              :option="barOption" autoresize class="min-w-200px flex-1"
              :style="{ minHeight: `${(barOption.yAxis.data.length * 30) + 100}px` }"
              @click="onChartClick"
            />
          </div>
        </Transition>
        <NButton round tertiary type="info" class="w-full" @click="toggleBar">
          {{ showBar ? '收起' : '查看详细' }}
        </NButton>
      </div>
    </n-card>
  </div>
  <BillDrawer v-model:show="drawerVisible" :query="drawerQuery" />
</template>

<script setup>
import { BarChart, PieChart } from 'echarts/charts'
import { GridComponent, LegendComponent, TitleComponent, ToolboxComponent } from 'echarts/components'
import { use } from 'echarts/core'
import { LabelLayout } from 'echarts/features'
import { CanvasRenderer } from 'echarts/renderers'
import { onMounted, ref } from 'vue'
import VChart from 'vue-echarts'
import BillDrawer from '@/components/BillAnalytics/BillDrawer.vue'
import { request } from '@/utils/http/request'

use([
  ToolboxComponent,
  TitleComponent,
  LegendComponent,
  PieChart,
  CanvasRenderer,
  LabelLayout,
  GridComponent,
  BarChart,
])

const dataInit = ref(false)

const pieOption = ref({
  title: {
    text: '收入分类 Top10',
    left: 'left',
    top: 10,
    textStyle: {
      fontSize: 14,
      fontWeight: 500,
      color: '#444', // 深灰更柔和
    },
  },
  tooltip: {
    trigger: 'item',
    formatter: '{b}<br/>金额：¥{c}（{d}%）',
  },
  legend: {
    top: 'bottom',
    itemWidth: 10,
    itemHeight: 10,
    textStyle: {
      fontSize: 12,
      color: '#666',
    },
  },
  toolbox: {
    show: false, // 简洁风格下建议去掉
  },
  series: [
    {
      name: '收入分类',
      type: 'pie',
      radius: [30, 80], // 更紧凑
      center: ['50%', '50%'], // 稍下移
      roseType: 'area',
      itemStyle: {
        borderRadius: 6,
        borderColor: '#fff',
        borderWidth: 2,
      },
      label: {
        color: '#444',
        fontSize: 12,
      },
      data: [],
      color: [
        '#9cd5c2',
        '#c5dafe',
        '#b5c6e0',
        '#fde2e2',
        '#f9dcc4',
        '#f6f5f5',
        '#d0e6a5',
        '#ffd3b6',
        '#cddafd',
        '#f8c8dc',
      ],
    },
  ],
})

const barOption = ref({
  title: {
    text: '全部分类收入概览',
    left: 'left',
    top: 10,
    textStyle: {
      fontSize: 15,
      fontWeight: 500,
      color: '#444', // 柔和字体色
    },
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow',
    },
    backgroundColor: '#fff',
    borderColor: '#eee',
    borderWidth: 1,
    textStyle: {
      color: '#333',
      fontSize: 12,
    },
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '6%',
    top: 50,
    containLabel: true,
  },
  xAxis: {
    type: 'value',
    axisLine: { lineStyle: { color: '#ddd' } },
    splitLine: { lineStyle: { color: '#eee' } },
    axisLabel: { color: '#666' },
  },
  yAxis: {
    type: 'category',
    data: [],
    axisLine: { show: false },
    axisTick: { show: false },
    axisLabel: { color: '#666' },
  },
  color: ['#aad9d9'], // 统一淡色调
  series: [
    {
      name: '金额',
      data: [],
      type: 'bar',
      barWidth: 14,
      itemStyle: {
        borderRadius: [4, 4, 4, 4],
      },
      label: {
        show: true,
        position: 'right',
        color: '#555',
        fontSize: 12,
      },
    },
  ],
})

// 控制条形图是否显示
const showBar = ref(false)

// 切换显示状态
function toggleBar() {
  showBar.value = !showBar.value
}

const drawerVisible = ref(false)
const drawerQuery = ref({})

function onChartClick(params) {
  drawerQuery.value = {
    trade_type: params.name,
    income_type: 1,
    trade_time: null,
  }
  drawerVisible.value = true
}

onMounted(async () => {
  const { code, data } = await request.post('/bill/get-income-categories', {})
  if (code === 0) {
    dataInit.value = true
    pieOption.value.series[0].data = []
    barOption.value.yAxis.data = []
    barOption.value.series[0].data = []
    let i = 0
    data.bill_records.forEach((item) => {
      if (i < 10) {
        pieOption.value.series[0].data.unshift({
          value: item.total,
          name: item.trade_type,
        })
      }
      i++
      barOption.value.yAxis.data.unshift(item.trade_type)
      barOption.value.series[0].data.unshift(item.total)
    })
  }
})
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
