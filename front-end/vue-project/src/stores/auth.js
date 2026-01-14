import { defineStore } from 'pinia'
import { ref } from 'vue'
import { authApi } from '@/api/authApi'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user_info')) || null)
  const token = ref(localStorage.getItem('access_token') || '')
  const isAuthenticated = ref(!!token.value)
  const router = useRouter()
  async function login(payload) {
    try {
      const response = await authApi.login(payload)
      const data = response.data

      // 1. Lưu vào State (Pinia)
      token.value = data.access_token
      user.value = data.user
      isAuthenticated.value = true

      //Lưu vào LocalStorage
      localStorage.setItem('access_token', data.access_token)
      localStorage.setItem('user_info', JSON.stringify(data.user))

      return data.user
    } catch (error) {
      throw error
    }
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch (error) {
      console.error('Lỗi khi gọi API logout:', error)
    } finally {
      // Reset State
      token.value = ''
      user.value = null
      isAuthenticated.value = false

      localStorage.removeItem('access_token')
      localStorage.removeItem('user_info')

      if (router) {
        router.push('/login')
      } else {
        window.location.href = '/login'
      }
    }
  }
  async function changePassword(passwordData) {
    try {
      const response = await authApi.changePassword(passwordData)
      return response
    } catch (error) {
      throw error
    }
  }
  return { user, token, isAuthenticated, login, logout, changePassword }
})
