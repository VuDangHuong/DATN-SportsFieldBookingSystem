<script setup>
import AdminSidebar from '@/components/AdminSidebar.vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

// Xử lý đăng xuất
const handleLogout = async () => {
  if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
    await authStore.logout()
    router.push('/login')
  }
}
</script>

<template>
  <div class="flex h-screen bg-gray-100 font-sans">
    <AdminSidebar />

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
        <h2 class="text-xl font-semibold text-gray-800">Hệ thống quản lý học phần nhóm</h2>

        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">Xin chào, <strong>Admin</strong></span>
          <button
            @click="handleLogout"
            class="px-3 py-1 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50 transition"
          >
            Đăng xuất
          </button>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
