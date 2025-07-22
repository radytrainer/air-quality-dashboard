import './assets/tailwind.css'

// import './assets/main.css'

import VueApexCharts from "vue3-apexcharts"

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import i18n from './i18n'

// ✅ import and create pinia
import { createPinia } from 'pinia'

const app = createApp(App)

// ✅ use pinia
const pinia = createPinia()
app.use(pinia)

// ✅ use router
app.use(router)

app.use(VueApexCharts)

app.use(i18n)

app.mount('#app')
