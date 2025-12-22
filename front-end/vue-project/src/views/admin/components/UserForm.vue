<script setup>
import { reactive, watch, toRefs } from 'vue'
// 1. Định nghĩa Props (Nhận từ cha)
const props = defineProps({
  show: Boolean,
  isEditing: Boolean,
  userData: Object,
})

// 2. Định nghĩa Events (Gửi lại cho cha)
const emit = defineEmits(['close', 'submit'])

// 3. Form Local State
const form = reactive({
  id: null,
  code: '',
  name: '',
  email: '',
  phone: '',
  role: 'student',
  is_active: true,
  password: '',
})
// Mỗi khi mở modal hoặc chọn user khác, cập nhật form local
watch(
  () => props.userData,
  (newUser) => {
    if (newUser && props.isEditing) {
      // Copy dữ liệu user vào form
      Object.assign(form, newUser)
      form.password = ''
    } else {
      resetForm()
    }
  },
  { deep: true },
) // deep: true để theo dõi object

const resetForm = () => {
  form.id = null
  form.code = ''
  form.name = ''
  form.email = ''
  form.phone = ''
  form.role = 'student'
  form.is_active = true
  form.password = ''
}
// 5. Xử lý Submit
const onSubmit = () => {
  // Gửi bản sao của form ra ngoài cho cha xử lý API
  emit('submit', { ...form })
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
  >
    <div
      class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="$emit('close')"
      ></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"
        >&#8203;</span
      >

      <div
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
      >
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            {{ isEditing ? 'Cập nhật thông tin' : 'Thêm người dùng mới' }}
          </h3>

          <form @submit.prevent="onSubmit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Mã số</label>
                <input
                  v-model="form.code"
                  type="text"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Họ và tên</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
              <input
                v-model="form.phone"
                type="text"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Vai trò</label>
                <select
                  v-model="form.role"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="student">Sinh viên</option>
                  <option value="lecturer">Giảng viên</option>
                  <option value="admin">Quản trị viên</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select
                  v-model="form.is_active"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option :value="true">Hoạt động</option>
                  <option :value="false">Khóa</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">
                {{
                  isEditing
                    ? 'Mật khẩu mới (Để trống nếu không đổi)'
                    : 'Mật khẩu (Mặc định: 123456789)'
                }}
              </label>
              <input
                v-model="form.password"
                type="password"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="onSubmit"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Lưu lại
          </button>
          <button
            @click="$emit('close')"
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Hủy
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
