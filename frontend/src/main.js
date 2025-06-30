import naive from 'naive-ui'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import '@unocss/reset/tailwind.css'
import 'uno.css'
import 'vfonts/FiraCode.css'

const app = createApp(App)

app.use(naive)
app.use(router)

app.mount('#app')
