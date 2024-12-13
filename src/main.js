import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from './App.vue'
import router from './router'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'
import '../public/style.css'
import '../public/script.js'

const app = createApp(App)
const pinia = createPinia()
// pinia.use(piniaPluginPersistedstate)

app.use(pinia)
app.use(router)
app.component('fai', FontAwesomeIcon)

library.add(fas)

app.mount('#app')
