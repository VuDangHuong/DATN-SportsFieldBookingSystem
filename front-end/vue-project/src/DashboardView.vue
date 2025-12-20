<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Hàm xử lý khi người dùng bấm nút Đăng xuất
const handleLogout = async () => {
  // 1. Gọi hàm logout từ Store và chờ nó hoàn tất
  await authStore.logout()

  // 2. Sau khi dữ liệu đã xóa sạch, chuyển hướng về trang Login
  router.push('/login')
}
</script>

<template>
  <div>
    <h1>Trang Dashboard</h1>
    <button @click="handleLogout" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
      Đăng xuất
    </button>
  </div>
</template>
