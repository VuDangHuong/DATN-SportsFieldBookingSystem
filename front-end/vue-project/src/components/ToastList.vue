<script setup>
import { useToastStore } from '@/stores/toast'
import { storeToRefs } from 'pinia'

const toastStore = useToastStore()
const { toasts } = storeToRefs(toastStore)
</script>

<template>
  <div class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="pointer-events-auto min-w-[300px] max-w-md p-4 rounded-lg shadow-lg text-white flex items-center justify-between cursor-pointer transform transition-all duration-300"
        :class="[
          toast.type === 'success'
            ? 'bg-green-500 border-l-4 border-green-700'
            : 'bg-red-500 border-l-4 border-red-700',
        ]"
        @click="toastStore.removeToast(toast.id)"
      >
        <div class="flex items-center">
          <svg
            v-if="toast.type === 'success'"
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>

          <span class="font-medium text-sm">{{ toast.message }}</span>
        </div>

        <button class="ml-4 opacity-70 hover:opacity-100">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
/* Hiệu ứng trượt vào từ bên phải */
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-enter-active {
  transition: all 0.3s ease-out;
}
.toast-enter-to {
  opacity: 1;
  transform: translateX(0);
}

/* Hiệu ứng mờ dần khi tắt */
.toast-leave-from {
  opacity: 1;
  transform: translateX(0);
}
.toast-leave-active {
  transition: all 0.3s ease-in;
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
