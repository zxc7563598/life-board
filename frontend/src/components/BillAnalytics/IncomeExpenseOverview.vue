<!-- 收入分类Top10 -->
<template>
  <div class="min-h-400px min-w-full flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="max-w-full w-full border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div>
        <div class="mb-3 text-base font-medium">
          <VChart :option="lineOption" autoresize class="min-h-400px min-w-full flex-1" @click="onChartClick" />
        </div>
      </div>
    </n-card>
  </div>
  <BillDrawer v-model:show="drawerVisible" :query="drawerQuery" />
</template>

<script setup>
import { LineChart } from 'echarts/charts'
import { GridComponent, LegendComponent, TitleComponent, ToolboxComponent, TooltipComponent } from 'echarts/components'
import { use } from 'echarts/core'
import { LabelLayout, UniversalTransition } from 'echarts/features'
import { CanvasRenderer } from 'echarts/renderers'
import { onMounted, ref } from 'vue'
import VChart from 'vue-echarts'
import BillDrawer from '@/components/BillAnalytics/BillDrawer.vue'
import { request } from '@/utils/http/request'

use([
  ToolboxComponent,
  TitleComponent,
  LegendComponent,
  TooltipComponent,
  CanvasRenderer,
  GridComponent,
  LineChart,
  UniversalTransition,
  LabelLayout,
])

const dataInit = ref(false)

const lineOption = ref({
  title: {
    text: '收入与支出趋势',
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
    backgroundColor: '#fff',
    borderColor: '#eee',
    borderWidth: 1,
    textStyle: {
      color: '#333',
      fontSize: 12,
    },
  },
  legend: {
    data: ['收入', '支出'],
    top: 10,
    right: 10,
    textStyle: {
      color: '#666',
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
    type: 'category',
    boundaryGap: false,
    data: [],
    axisLine: { lineStyle: { color: '#ddd' } },
    axisLabel: { color: '#666' },
  },
  yAxis: {
    type: 'value',
    axisLine: { show: false },
    splitLine: { lineStyle: { color: '#eee' } },
    axisLabel: { color: '#666' },
  },
  color: ['#9cd5c2', '#f8c8dc'], // 柔和的收入 / 支出配色
  series: [
    {
      name: '收入',
      type: 'line',
      smooth: true,
      symbol: 'circle',
      symbolSize: 6,
      data: [],
      lineStyle: { width: 2 },
      areaStyle: {
        color: 'rgba(156, 213, 194, 0.2)',
      },
    },
    {
      name: '支出',
      type: 'line',
      smooth: true,
      symbol: 'circle',
      symbolSize: 6,
      data: [],
      lineStyle: { width: 2 },
      areaStyle: {
        color: 'rgba(248, 200, 220, 0.2)',
      },
    },
  ],
})

const lineData = ref([])
const drawerVisible = ref(false)
const drawerQuery = ref({})

function onChartClick(params) {
  drawerQuery.value = {
    trade_type: null,
    income_type: params.seriesIndex === 0 ? 1 : 2,
    trade_time: [lineData.value[params.dataIndex].start_timestamp * 1000, lineData.value[params.dataIndex].end_timestamp * 1000],
  }
  drawerVisible.value = true
}

onMounted(async () => {
  const { code, data } = await request.post('/bill/get-financial-summary', {})
  if (code === 0) {
    dataInit.value = true
    lineOption.value.xAxis.data = []
    lineOption.value.series[0].data = []
    lineOption.value.series[1].data = []
    lineData.value = data.date_range
    data.date_range.forEach((item) => {
      lineOption.value.xAxis.data.push(item.range)
      lineOption.value.series[0].data.push(item.income.total_amount)
      lineOption.value.series[1].data.push(item.expense.total_amount)
    })
  }
})
</script>
