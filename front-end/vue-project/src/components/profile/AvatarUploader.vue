<script setup>
import { ref } from 'vue'
import { authApi } from '@/api/authApi'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import { getAvatarUrl } from '@/utils/imageHelper'
import SvgIcon from '@/components/icons/SVG.vue'

const emit = defineEmits(['close'])

const authStore = useAuthStore()
const toast = useToastStore()

// State
const fileInput = ref(null)
const previewUrl = ref(null)
const selectedFile = ref(null)
const loading = ref(false)

// Kích hoạt ô chọn file khi bấm vào icon máy ảnh
const triggerFileInput = () => {
  fileInput.value.click()
}

// Xử lý khi người dùng chọn file
const onFileChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate cơ bản phía client
  if (file.size > 2 * 1024 * 1024) {
    // 2MB
    toast.error('Ảnh quá lớn. Vui lòng chọn ảnh dưới 2MB.')
    return
  }
  if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
    toast.error('Định dạng không hỗ trợ.')
    return
  }

  // Lưu file vào biến
  selectedFile.value = file

  // Tạo URL ảo để xem trước ngay lập tức
  previewUrl.value = URL.createObjectURL(file)
}

//Gọi API Upload
const handleUploadAvatar = async () => {
  console.log('File chuẩn bị gửi:', selectedFile.value)

  if (!selectedFile.value) {
    alert('Chưa có file nào được chọn!')
    return
  }

  loading.value = true
  try {
    const formData = new FormData()
    formData.append('avatar', selectedFile.value)

    await authStore.updateAvatar(formData)

    toast.success('Cập nhật ảnh đại diện thành công!')

    // Reset trạng thái
    selectedFile.value = null
    previewUrl.value = null

    emit('close')
  } catch (error) {
    toast.error(error.response?.data?.message || 'Lỗi upload ảnh.')
  } finally {
    loading.value = false
  }
}

const cancelUpload = () => {
  selectedFile.value = null
  previewUrl.value = null
  if (fileInput.value) fileInput.value.value = ''
}
</script>

<template>
  <div class="flex flex-col items-center gap-4 p-6 bg-white rounded-lg shadow">
    <div class="relative group">
      <img
        class="h-32 w-32 rounded-full object-cover border-4 border-gray-100 shadow-sm"
        :src="previewUrl || getAvatarUrl(authStore.user)"
        alt="User Avatar"
      />

      <div
        @click="triggerFileInput"
        class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-white"
      >
        <SvgIcon name="camera" class="h-8 w-8" />
      </div>

      <button
        @click="triggerFileInput"
        class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full shadow-lg hover:bg-blue-700 transition"
        title="Đổi ảnh"
      >
        <SvgIcon name="camera" class="h-4 w-4" />
      </button>

      <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="onFileChange" />
    </div>

    <div v-if="selectedFile" class="flex gap-2 animate-fade-in-up">
      <button
        @click="handleUploadAvatar"
        :disabled="loading"
        class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
      >
        <SvgIcon v-if="loading" name="spinner" spin class="mr-2" />
        {{ loading ? 'Đang tải lên...' : 'Lưu ảnh' }}
      </button>

      <button
        @click="cancelUpload"
        :disabled="loading"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
      >
        Hủy
      </button>
    </div>

    <div v-else class="text-center">
      <h3 class="font-bold text-gray-800">{{ authStore.user?.name }}</h3>
      <p class="text-sm text-gray-500">{{ authStore.user?.role }}</p>
    </div>
  </div>
</template>
