<script setup>
import AvatarUploader from '@/components/profile/AvatarUploader.vue'

// Nhận prop 'show' từ cha để biết khi nào hiển thị
defineProps({
  show: {
    type: Boolean,
    default: false,
  },
})

// Định nghĩa sự kiện để báo cho cha biết cần đóng modal
const emit = defineEmits(['close'])

const closeModal = () => {
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <transition name="fade">
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
      >
        <div class="absolute inset-0" @click="closeModal"></div>

        <div
          class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up"
        >
          <div class="flex justify-between items-center px-4 py-3 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-700">Đổi ảnh đại diện</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
              ✕
            </button>
          </div>

          <div class="p-1">
            <AvatarUploader @close="closeModal" />
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<style scoped>
/* Hiệu ứng Fade cho nền đen */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Hiệu ứng trượt lên cho hộp thoại */
.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
