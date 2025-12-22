import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])

  // Hàm thêm thông báo
  function addToast(message, type = 'success') {
    const id = Date.now() // Tạo ID duy nhất

    // Thêm vào mảng
    toasts.value.push({
      id,
      message,
      type,
    })

    // Tự động xóa sau 3 giây (3000ms)
    setTimeout(() => {
      removeToast(id)
    }, 3000)
  }

  // Hàm xóa thông báo (khi hết giờ hoặc user bấm tắt)
  function removeToast(id) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  // Wrapper cho tiện dùng
  function success(message) {
    addToast(message, 'success')
  }

  function error(message) {
    addToast(message, 'error')
  }

  return { toasts, addToast, removeToast, success, error }
})
