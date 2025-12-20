// src/api/axiosClient.js
import axios from 'axios'

const axiosClient = axios.create({
  baseURL: 'http://127.0.0.1:8000/api', // Thay bằng link API của bạn
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// --- PHẦN QUAN TRỌNG NHẤT ĐỂ LOGOUT HOẠT ĐỘNG ---
// Trước khi gửi request, kiểm tra xem có token không
axiosClient.interceptors.request.use(
  (config) => {
    // Lấy token từ LocalStorage
    const token = localStorage.getItem('access_token')

    // Nếu có token, gắn vào Header theo chuẩn Bearer
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)
// ------------------------------------------------

export default axiosClient
