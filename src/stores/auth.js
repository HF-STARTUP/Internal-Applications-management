import apiClient from '@/api'
import router from '@/router'
import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

export const useAuthStore = defineStore('auth', {
  state: () => {
    let initialState = {
      user: null || JSON.parse(localStorage.getItem('user')),
      token: computed(() => null || localStorage.getItem('current_token') || sessionStorage.getItem('current_token')),
      isDisconnecting: ref(false),
      apiServer: 'http://192.168.50.176:8000/api/',
      backendServer: 'http://192.168.50.176:8000'
    }
    return initialState;
  },

  actions: {
    setUser(user) {
      localStorage.setItem('user', JSON.stringify(user))
    },
    setToken(token, save) {
      if (save) {
        localStorage.setItem('current_token', token);
      } else {
        sessionStorage.setItem('current_token', token);
      }
    },
    async logout() {
      this.isDisconnecting = true
      try {
        await apiClient.delete('/users/logout')
        router.push('/')
        localStorage.clear()
        sessionStorage.clear()
        this.setToken(null)
        console.clear()
      } catch (error) {
        console.error('Erreur lors de la déconnexion: ', error)
      } finally {
        this.isDisconnecting = false
      }
    },
  },
  persist: true,
})
