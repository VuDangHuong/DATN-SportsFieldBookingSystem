<script setup>
import { ref, reactive, onMounted, watch, computed } from 'vue'
import { useUserStore } from '@/stores/admin/user' // 1. Import Store
import { storeToRefs } from 'pinia' // 2. Import helper của Pinia
import UserForm from './components/UserForm.vue'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
// --- KẾT NỐI STORE ---
const userStore = useUserStore()
// Lấy State (Dữ liệu) từ Store
const { users, loading, pagination, filters } = storeToRefs(userStore)
// Lấy Actions (Hàm) từ Store
const {
  fetchUsers,
  createUser,
  updateUser,
  deleteUser,
  toggleStatus,
  importUsers,
  setPage,
  setFilterRole,
} = userStore

// --- LOCAL STATE (Biến cục bộ dùng cho giao diện này) ---
const showImportModal = ref(false)
const importFile = ref(null)
const showFormModal = ref(false)
const isEditing = ref(false)
const selectedUser = ref(null)

// --- LOGIC PHÂN TRANG THÔNG MINH ---
const visiblePages = computed(() => {
  const { current_page, last_page } = pagination.value // Lưu ý: dùng .value vì lấy từ storeToRefs
  const delta = 1
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= last_page; i++) {
    if (i === 1 || i === last_page || (i >= current_page - delta && i <= current_page + delta)) {
      range.push(i)
    }
  }

  for (let i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push('...')
      }
    }
    rangeWithDots.push(i)
    l = i
  }
  return rangeWithDots
})

// --- HELPER FUNCTIONS ---
function debounce(func, timeout = 300) {
  let timer
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => {
      func.apply(this, args)
    }, timeout)
  }
}

const handleSearch = debounce(() => {
  filters.value.page = 1 // Reset về trang 1 khi search
  fetchUsers()
}, 500)

// --- XỬ LÝ MODAL FORM ---
const openCreateModal = () => {
  isEditing.value = false
  selectedUser.value = {}
  showFormModal.value = true
}

const openEditModal = (user) => {
  isEditing.value = true
  selectedUser.value = { ...user }
  showFormModal.value = true
}

const handleSaveUser = async (formData) => {
  let result
  // Gọi hàm từ Store thay vì gọi API trực tiếp
  if (isEditing.value) {
    result = await updateUser(formData.id, formData)
  } else {
    result = await createUser(formData)
  }

  if (result.success) {
    toast.success(result.message)
    showFormModal.value = false
  } else {
    toast.error(result.message)
  }
}

// --- CÁC HÀM BẠN ĐANG THIẾU (Wrapper gọi Store) ---

// 1. Xử lý xóa (handleDelete)
const handleDelete = async (id) => {
  if (!confirm('Bạn có chắc muốn xóa người dùng này?')) return
  const result = await deleteUser(id)
  if (result.success) {
    toast.success(result.message)
    showFormModal.value = false
  } else {
    toast.error(result.message || 'Xóa thất bại')
  }
}

// 2. Xử lý đổi trạng thái (handleStatusToggle)
const handleStatusToggle = async (user) => {
  try {
    await toggleStatus(user)
    toast.success('Đã cập nhật trạng thái')
  } catch (e) {
    toast.error('Không thể thay đổi trạng thái.')
  }
}

// 3. Xử lý Import (handleImportFile)
const handleImportFile = async () => {
  if (!importFile.value) return alert('Vui lòng chọn file')
  const formData = new FormData()
  formData.append('file', importFile.value)

  const result = await importUsers(formData)
  if (result.success) {
    toast.success(result.message)
    showImportModal.value = false
  } else {
    toast.error(result.message)
  }
}

const onFileChange = (e) => {
  importFile.value = e.target.files[0]
}

// Hàm đổi trang
const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    setPage(page) // Gọi hàm setPage của Store
  }
}

// Watch Role (Cập nhật qua Store helper)
watch(
  () => filters.value.role,
  (newRole) => {
    setFilterRole(newRole)
  },
)

// Khởi tạo
onMounted(() => {
  fetchUsers()
})
</script>

<template>
  <div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Quản lý người dùng</h1>
      <div class="flex gap-2">
        <button
          @click="showImportModal = true"
          class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
            />
          </svg>
          Import Excel
        </button>
        <button
          @click="openCreateModal"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            />
          </svg>
          Thêm mới
        </button>
      </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center">
      <div class="flex bg-gray-100 p-1 rounded-lg overflow-x-auto max-w-full">
        <button
          v-for="role in ['all', 'lecturer', 'student', 'admin']"
          :key="role"
          @click="filters.role = role"
          :class="[
            'px-4 py-1.5 rounded-md text-sm font-medium transition-colors whitespace-nowrap',
            filters.role === role
              ? 'bg-white text-blue-600 shadow-sm'
              : 'text-gray-500 hover:text-gray-700',
          ]"
        >
          {{
            role === 'all'
              ? 'Tất cả'
              : role === 'lecturer'
                ? 'Giảng viên'
                : role === 'admin'
                  ? 'Quản trị viên'
                  : 'Sinh viên'
          }}
        </button>
      </div>
      <div class="flex-1 min-w-[200px] relative">
        <span class="absolute left-3 top-2.5 text-gray-400">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
        </span>
        <input
          v-model="filters.search"
          @input="handleSearch"
          type="text"
          placeholder="Tìm theo tên, email, mã số..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
        />
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Thông tin
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Vai trò
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Liên hệ
              </th>
              <th
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Trạng thái
              </th>
              <th
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Hành động
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="5" class="text-center py-8 text-gray-500">Đang tải dữ liệu...</td>
            </tr>
            <tr v-else-if="users.length === 0">
              <td colspan="5" class="text-center py-8 text-gray-500">
                Không tìm thấy kết quả nào.
              </td>
            </tr>

            <tr
              v-for="user in users"
              :key="user.id + '-desktop'"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img
                      class="h-10 w-10 rounded-full object-cover"
                      :src="user.avatar || 'https://ui-avatars.com/api/?name=' + user.name"
                      alt=""
                    />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    user.role === 'admin'
                      ? 'bg-red-100 text-red-800'
                      : user.role === 'lecturer'
                        ? 'bg-purple-100 text-purple-800'
                        : 'bg-green-100 text-green-800',
                  ]"
                >
                  {{
                    user.role === 'lecturer'
                      ? 'Giảng viên'
                      : user.role === 'admin'
                        ? 'Quản trị'
                        : 'Sinh viên'
                  }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.email }}</div>
                <div class="text-sm text-gray-500">{{ user.phone || '---' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <button
                  @click="handleStatusToggle(user)"
                  :class="[
                    'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none',
                    user.is_active ? 'bg-blue-600' : 'bg-gray-200',
                  ]"
                >
                  <span
                    aria-hidden="true"
                    :class="[
                      'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      user.is_active ? 'translate-x-5' : 'translate-x-0',
                    ]"
                  />
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openEditModal(user)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Sửa
                </button>
                <button @click="handleDelete(user.id)" class="text-red-600 hover:text-red-900">
                  Xóa
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="md:hidden">
        <div v-if="loading" class="p-4 text-center text-gray-500">Đang tải dữ liệu...</div>
        <div v-else-if="users.length === 0" class="p-4 text-center text-gray-500">
          Không tìm thấy kết quả nào.
        </div>

        <div v-else class="space-y-4 p-4 bg-gray-50">
          <div
            v-for="user in users"
            :key="user.id + '-mobile'"
            class="bg-white rounded-lg shadow border border-gray-100 overflow-hidden"
          >
            <div class="flex items-center p-4 border-b border-gray-100">
              <img
                class="h-12 w-12 rounded-full object-cover"
                :src="user.avatar || 'https://ui-avatars.com/api/?name=' + user.name"
                alt=""
              />
              <div class="ml-3 flex-1">
                <div class="text-sm font-bold text-gray-900">{{ user.name }}</div>
                <div class="text-xs text-gray-500 font-mono">{{ user.code }}</div>
              </div>
              <span
                :class="[
                  'px-2 py-1 text-xs font-semibold rounded-full',
                  user.role === 'admin'
                    ? 'bg-red-100 text-red-800'
                    : user.role === 'lecturer'
                      ? 'bg-purple-100 text-purple-800'
                      : 'bg-green-100 text-green-800',
                ]"
              >
                {{ user.role === 'lecturer' ? 'GV' : user.role === 'admin' ? 'Admin' : 'SV' }}
              </span>
            </div>

            <div class="p-4 space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-500">Email:</span>
                <span class="text-gray-900 font-medium truncate ml-2 max-w-[200px]">{{
                  user.email
                }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-500">SĐT:</span>
                <span class="text-gray-900">{{ user.phone || '---' }}</span>
              </div>
            </div>

            <div
              class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-100"
            >
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">{{
                  user.is_active ? 'Hoạt động' : 'Đang khóa'
                }}</span>
                <button
                  @click="handleStatusToggle(user)"
                  :class="[
                    'relative inline-flex flex-shrink-0 h-5 w-9 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none',
                    user.is_active ? 'bg-blue-600' : 'bg-gray-300',
                  ]"
                >
                  <span
                    aria-hidden="true"
                    :class="[
                      'pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                      user.is_active ? 'translate-x-4' : 'translate-x-0',
                    ]"
                  />
                </button>
              </div>

              <div class="flex gap-3">
                <button
                  @click="openEditModal(user)"
                  class="text-indigo-600 hover:text-indigo-900 text-sm font-medium flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                  </svg>
                  Sửa
                </button>
                <button
                  @click="handleDelete(user.id)"
                  class="text-red-600 hover:text-red-900 text-sm font-medium flex items-center"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                  Xóa
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      class="mt-4 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
      v-if="pagination.last_page > 1"
    >
      <div class="flex flex-1 justify-between sm:hidden">
        <button
          @click="changePage(filters.page - 1)"
          :disabled="filters.page === 1"
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
        >
          Trước
        </button>
        <button
          @click="changePage(filters.page + 1)"
          :disabled="filters.page === pagination.last_page"
          class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
        >
          Sau
        </button>
      </div>

      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            Hiển thị trang
            <span class="font-medium">{{ filters.page }}</span>
            trên tổng số
            <span class="font-medium">{{ pagination.last_page }}</span>
            trang ({{ pagination.total }} kết quả)
          </p>
        </div>

        <div>
          <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <button
              @click="changePage(filters.page - 1)"
              :disabled="filters.page === 1"
              class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Previous</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path
                  fill-rule="evenodd"
                  d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>

            <template v-for="(page, index) in visiblePages" :key="index">
              <span
                v-if="page === '...'"
                class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
                >...</span
              >

              <button
                v-else
                @click="changePage(page)"
                :class="[
                  page === filters.page
                    ? 'relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
                    : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0',
                ]"
              >
                {{ page }}
              </button>
            </template>

            <button
              @click="changePage(filters.page + 1)"
              :disabled="filters.page === pagination.last_page"
              class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Next</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path
                  fill-rule="evenodd"
                  d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <UserForm
      :show="showFormModal"
      :is-editing="isEditing"
      :user-data="selectedUser"
      @close="showFormModal = false"
      @submit="handleSaveUser"
    />

    <div
      v-if="showImportModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-xl w-96">
        <h3 class="text-lg font-bold mb-4">Import danh sách từ Excel</h3>
        <input
          type="file"
          @change="onFileChange"
          accept=".xlsx, .xls"
          class="mb-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
        />
        <div class="flex justify-end gap-2">
          <button
            @click="showImportModal = false"
            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded"
          >
            Hủy
          </button>
          <button
            @click="handleImportFile"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
          >
            Upload
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
