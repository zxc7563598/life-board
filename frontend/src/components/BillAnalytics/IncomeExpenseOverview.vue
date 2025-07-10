<!-- 收入分类Top10 -->
<template>
  <div class="min-h-400px min-w-800px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
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
import { computed, onMounted, ref, watch } from 'vue'
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

const isDark = window.$isDark

const dataInit = ref(false)

const lineOptionColor = computed(() => ({
  titleColor: isDark.value ? '#d6d6d6' : '#444',
  tooltipBackgroundColor: isDark.value ? '#1a1a1a' : '#fff',
  tooltipBorderColor: isDark.value ? '#333333' : '#eee',
  tooltipColor: isDark.value ? '#cccccc' : '#333',
  legendColor: isDark.value ? '#bbbbbb' : '#666',
  xAxisAxisLineColor: isDark.value ? '#444444' : '#ddd',
  xAxisAxisLabelColor: isDark.value ? '#bbbbbb' : '#666',
  yAxisSplitLineColor: isDark.value ? '#cccccc' : '#eee',
  yAxisAxisLabelColor: isDark.value ? '#bbbbbb' : '#666',
  color: isDark.value ? ['#6bd3ba', '#d69caf'] : ['#9cd5c2', '#f8c8dc'],
  seriesColor: isDark.value ? ['rgba(107, 211, 186, 0.25)', 'rgba(214, 156, 175, 0.25)'] : ['rgba(156, 213, 194, 0.2)', 'rgba(248, 200, 220, 0.2)'],
}))

const lineOption = ref({
  title: {
    text: '收入与支出趋势',
    left: 'left',
    top: 10,
    textStyle: {
      fontSize: 15,
      fontWeight: 500,
      color: lineOptionColor.value.titleColor,
    },
  },
  tooltip: {
    trigger: 'axis',
    backgroundColor: lineOptionColor.value.tooltipBackgroundColor,
    borderColor: lineOptionColor.value.tooltipBorderColor,
    borderWidth: 1,
    textStyle: {
      color: lineOptionColor.value.tooltipColor,
      fontSize: 12,
    },
  },
  legend: {
    data: ['收入', '支出'],
    top: 10,
    right: 10,
    textStyle: {
      color: lineOptionColor.value.legendColor,
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
    axisLine: { lineStyle: { color: lineOptionColor.value.xAxisAxisLineColor } },
    axisLabel: { color: lineOptionColor.value.xAxisAxisLabelColor },
  },
  yAxis: {
    type: 'value',
    axisLine: { show: false },
    splitLine: { lineStyle: { color: lineOptionColor.value.yAxisSplitLineColor } },
    axisLabel: { color: lineOptionColor.value.yAxisAxisLabelColor },
  },
  color: [lineOptionColor.value.color[0], lineOptionColor.value.color[1]], // 柔和的收入 / 支出配色
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
        color: lineOptionColor.value.seriesColor[0],
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
        color: lineOptionColor.value.seriesColor[1],
      },
    },
  ],
})

watch(isDark, () => {
  lineOption.value.title.textStyle.color = lineOptionColor.value.titleColor
  lineOption.value.tooltip.backgroundColor = lineOptionColor.value.tooltipBackgroundColor
  lineOption.value.tooltip.borderColor = lineOptionColor.value.tooltipBorderColor
  lineOption.value.tooltip.textStyle.color = lineOptionColor.value.tooltipColor
  lineOption.value.legend.textStyle.color = lineOptionColor.value.legendColor
  lineOption.value.xAxis.axisLine.lineStyle.color = lineOptionColor.value.xAxisAxisLineColor
  lineOption.value.xAxis.axisLabel.color = lineOptionColor.value.xAxisAxisLabelColor
  lineOption.value.yAxis.splitLine.lineStyle.color = lineOptionColor.value.yAxisSplitLineColor
  lineOption.value.yAxis.axisLabel.color = lineOptionColor.value.yAxisAxisLabelColor
  lineOption.value.color = lineOptionColor.value.color
  lineOption.value.series[0].areaStyle.color = lineOptionColor.value.seriesColor[0]
  lineOption.value.series[1].areaStyle.color = lineOptionColor.value.seriesColor[1]
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
