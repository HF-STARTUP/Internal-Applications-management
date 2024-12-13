import axios from 'axios'
import { createPinia, setActivePinia } from 'pinia'
const pinia = createPinia()
setActivePinia(pinia)
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()
const apiClient = axios.create({
  baseURL: authStore.apiServer,
})

apiClient.interceptors.request.use(
  config => {
    const authStore = useAuthStore()
    config.headers['Authorization'] = `Bearer ${authStore.token}`
    return config
  },
  error => {
    return Promise.reject(error)
  },
)

export default apiClient
