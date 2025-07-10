<template>
  <div class="h-auto min-w-800px flex-1">
    <n-card
      class="h-full max-w-full w-auto border border-gray-200 rounded-xl shadow-lg transition duration-300 dark:border-gray-700 dark:shadow-[0_4px_12px_rgba(0,0,0,0.4)]"
    >
      <div class="h-full w-full flex items-center justify-center">
        <div
          class="font-orbitron select-none text-center text-[8vw] text-gray-800 drop-shadow-[0_0_10px_#bde3ff] lg:text-[4vw] md:text-[5vw] dark:text-cyan-300 dark:drop-shadow-[0_0_12px_#0ff]"
        >
          {{ timeString }}
        </div>
      </div>
    </n-card>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const timeString = ref('')

function formatTime(date) {
  return date.toLocaleTimeString('zh-CN', { hour12: false })
}

let timer = null

onMounted(() => {
  timeString.value = formatTime(new Date())
  timer = setInterval(() => {
    timeString.value = formatTime(new Date())
  }, 1000)
})

onUnmounted(() => {
  if (timer)
    clearInterval(timer)
})
</script>

<style>
/* latin */
@font-face {
  font-family: 'Orbitron';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url('/fonts/Orbitron.woff2') format('woff2');
  unicode-range:
    U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC,
    U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

.font-orbitron {
  font-family: 'Orbitron', sans-serif;
}
</style>
