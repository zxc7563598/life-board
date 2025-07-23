<!-- 支出分类Top10 -->
<template>
  <div class="min-h-380px min-w-600px flex-1 opacity-0 duration-1500" :class="dataInit ? 'opacity-100' : ''">
    <n-card
      class="max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div>
        <div class="mb-3 text-base font-medium">
          <VChart :option="pieOption" autoresize class="min-h-380px min-w-600px flex-1" @click="onChartClick" />
        </div>
        <Transition name="fade-slide" mode="out-in">
          <div v-if="showBar" key="bar" class="mb-3 text-base font-medium">
            <VChart
              :option="barOption" autoresize class="min-w-600px flex-1"
              :style="{ minHeight: `${(barOption.yAxis.data.length * 30) + 100}px` }" @click="onChartClick"
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
import { GridComponent, LegendComponent, TitleComponent, ToolboxComponent, TooltipComponent } from 'echarts/components'
import { use } from 'echarts/core'
import { LabelLayout } from 'echarts/features'
import { CanvasRenderer } from 'echarts/renderers'
import { computed, onMounted, ref, watch } from 'vue'
import VChart from 'vue-echarts'
import BillDrawer from '@/components/BillAnalytics/BillDrawer.vue'
import { request } from '@/utils/http/request'

use([
  ToolboxComponent,
  TooltipComponent,
  TitleComponent,
  LegendComponent,
  PieChart,
  CanvasRenderer,
  LabelLayout,
  GridComponent,
  BarChart,
])

const isDark = window.$isDark

const dataInit = ref(false)

const pieOptionColor = computed(() => ({
  titleColor: isDark.value ? '#d6d6d6' : '#444',
  legendColor: isDark.value ? '#bbbbbb' : '#666',
  itemStyleColor: isDark.value ? '#1a1a1a' : '#fff',
  labelColor: isDark.value ? '#d6d6d6' : '#444',
  dataColor: isDark.value
    ? [
        '#6bd3ba',
        '#7faeea',
        '#688bb5',
        '#d69cac',
        '#d1a857',
        '#999999',
        '#a1c25d',
        '#da9b7b',
        '#7d97d6',
        '#d69caf',
      ]
    : [
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
  tooltipBackgroundColor: isDark.value ? '#1a1a1a' : '#fff',
  tooltipBorderColor: isDark.value ? '#333333' : '#eee',
  tooltipColor: isDark.value ? '#cccccc' : '#333',
}))

const pieOption = ref({
  title: {
    text: '支出分类 Top10',
    left: 'left',
    top: 10,
    textStyle: {
      fontSize: 14,
      fontWeight: 500,
      color: pieOptionColor.value.titleColor,
    },
  },
  tooltip: {
    trigger: 'item',
    formatter: '{b}<br/>金额：¥{c}（{d}%）',
    backgroundColor: pieOptionColor.value.tooltipBackgroundColor,
    borderColor: pieOptionColor.value.tooltipBorderColor,
    borderWidth: 1,
    textStyle: {
      color: pieOptionColor.value.tooltipColor,
      fontSize: 12,
    },
  },
  legend: {
    top: 'bottom',
    itemWidth: 10,
    itemHeight: 10,
    textStyle: {
      fontSize: 12,
      color: pieOptionColor.value.legendColor,
    },
  },
  series: [
    {
      name: '收入分类',
      type: 'pie',
      radius: [30, 80],
      center: ['50%', '50%'],
      roseType: 'area',
      itemStyle: {
        borderRadius: 6,
        borderColor: pieOptionColor.value.itemStyleColor,
        borderWidth: 2,
      },
      label: {
        color: pieOptionColor.value.labelColor,
        fontSize: 12,
      },
      data: [],
      color: pieOptionColor.value.dataColor,
    },
  ],
})

const barOptionColor = computed(() => ({
  titleColor: isDark.value ? '#d6d6d6' : '#444',
  tooltipBackgroundColor: isDark.value ? '#1a1a1a' : '#fff',
  tooltipBorderColor: isDark.value ? '#333333' : '#eee',
  tooltipColor: isDark.value ? '#cccccc' : '#333',
  xAxisAxisLineColor: isDark.value ? '#444444' : '#ddd',
  xAxisSplitLineColor: isDark.value ? '#333333' : '#eee',
  xAxisAxisLabelColor: isDark.value ? '#bbbbbb' : '#666',
  yAxisAxisLabelColor: isDark.value ? '#bbbbbb' : '#666',
  color: isDark.value ? '#6bb3b3' : '#aad9d9',
  seriesLabelColor: isDark.value ? '#cccccc' : '#555',
}))

const barOption = ref({
  title: {
    text: '全部分类支出概览',
    left: 'left',
    top: 10,
    textStyle: {
      fontSize: 15,
      fontWeight: 500,
      color: barOptionColor.value.titleColor,
    },
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'shadow',
    },
    backgroundColor: barOptionColor.value.tooltipBackgroundColor,
    borderColor: barOptionColor.value.tooltipBorderColor,
    borderWidth: 1,
    textStyle: {
      color: barOptionColor.value.tooltipColor,
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
    axisLine: { lineStyle: { color: barOptionColor.value.xAxisAxisLineColor } },
    splitLine: { lineStyle: { color: barOptionColor.value.xAxisSplitLineColor } },
    axisLabel: { color: barOptionColor.value.xAxisAxisLabelColor },
  },
  yAxis: {
    type: 'category',
    data: [],
    axisLine: { show: false },
    axisTick: { show: false },
    axisLabel: { color: barOptionColor.value.yAxisAxisLabelColor },
  },
  color: [barOptionColor.value.color],
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
        color: barOptionColor.value.seriesLabelColor,
        fontSize: 12,
      },
    },
  ],
})

watch(isDark, () => {
  pieOption.value.title.textStyle.color = pieOptionColor.value.titleColor
  pieOption.value.legend.textStyle.color = pieOptionColor.value.legendColor
  pieOption.value.tooltip.backgroundColor = barOptionColor.value.tooltipBackgroundColor
  pieOption.value.tooltip.borderColor = barOptionColor.value.tooltipBorderColor
  pieOption.value.tooltip.textStyle.color = barOptionColor.value.tooltipColor
  pieOption.value.series[0].itemStyle.borderColor = pieOptionColor.value.itemStyleColor
  pieOption.value.series[0].label.color = pieOptionColor.value.labelColor
  pieOption.value.series[0].color = pieOptionColor.value.dataColor

  barOption.value.title.textStyle.color = barOptionColor.value.titleColor
  barOption.value.tooltip.backgroundColor = barOptionColor.value.tooltipBackgroundColor
  barOption.value.tooltip.borderColor = barOptionColor.value.tooltipBorderColor
  barOption.value.tooltip.textStyle.color = barOptionColor.value.tooltipColor
  barOption.value.xAxis.axisLine.lineStyle.color = barOptionColor.value.xAxisAxisLineColor
  barOption.value.xAxis.splitLine.lineStyle.color = barOptionColor.value.xAxisSplitLineColor
  barOption.value.xAxis.axisLabel.color = barOptionColor.value.xAxisAxisLabelColor
  barOption.value.yAxis.axisLabel.color = barOptionColor.value.yAxisAxisLabelColor
  barOption.value.color[0] = barOptionColor.value.color
  barOption.value.series[0].label.color = barOptionColor.value.seriesLabelColor
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
    income_type: 2,
    trade_time: null,
  }
  drawerVisible.value = true
}

onMounted(async () => {
  const { code, data } = await request.post('/bill/get-expense-categories', {})
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
  transition: all 0.5s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
