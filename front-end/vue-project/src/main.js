// File: src/main.js
import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue' // SỬA LẠI: Import App.vue, không phải DashboardView
import router from './router'

const app = createApp(App) // Mount App

app.use(createPinia())
app.use(router)

app.mount('#app')
