// src/stores/auth.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { authApi } from '@/api/authApi'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  // Lấy dữ liệu từ LocalStorage khi khởi tạo để giữ trạng thái F5
  const user = ref(JSON.parse(localStorage.getItem('user_info')) || null)
  const token = ref(localStorage.getItem('access_token') || '')
  const isAuthenticated = ref(!!token.value) // Chuyển đổi sang boolean (true/false)

  // --- ACTION LOGIN ---
  async function login(payload) {
    try {
      const response = await authApi.login(payload)
      const data = response.data

      // 1. Lưu vào State (Pinia)
      token.value = data.access_token
      user.value = data.user
      isAuthenticated.value = true

      // 2. Lưu vào LocalStorage (Để axiosClient lấy được ở Bước 1)
      localStorage.setItem('access_token', data.access_token)
      localStorage.setItem('user_info', JSON.stringify(data.user))

      return data.user
    } catch (error) {
      throw error
    }
  }

  // --- ACTION LOGOUT (ĐÃ SỬA) ---
  async function logout() {
    try {
      // 1. Gọi API Logout
      // Nhờ axiosClient ở Bước 1, Token sẽ tự động được gắn vào header
      // Laravel sẽ nhận được: Authorization: Bearer <token>
      await authApi.logout()
    } catch (error) {
      console.error('Lỗi khi gọi API logout:', error)
      // Dù API lỗi (ví dụ token hết hạn), ta vẫn phải xóa data ở client để user thoát ra
    } finally {
      // 2. Reset State (Pinia)
      token.value = ''
      user.value = null
      isAuthenticated.value = false

      // 3. Xóa LocalStorage (Quan trọng)
      localStorage.removeItem('access_token')
      localStorage.removeItem('user_info')

      // (Tùy chọn) Chuyển hướng về trang login nếu cần thiết tại đây,
      // hoặc xử lý việc này ở phía Component gọi hàm logout
      // const router = useRouter();
      // router.push('/login');
    }
  }

  return { user, token, isAuthenticated, login, logout }
})
