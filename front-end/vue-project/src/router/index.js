import { createRouter, createWebHistory } from 'vue-router'
// import { useAuthStore } from '@/stores/auth'

// SỬA LẠI ĐƯỜNG DẪN IMPORT CHO ĐÚNG VỚI CẤU TRÚC THƯ MỤC
// import LoginView from '../views/auth/LoginView.vue'
// import RegisterView from '../views/auth/RegisterView.vue'
// import ForgotPasswordView from '../views/auth/ForgotPasswordView.vue'
// import ResetPasswordView from '../views/auth/ResetPasswordView.vue'
import DashboardView from '../DashboardView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // {
    //   path: '/login',
    //   name: 'login',
    //   component: LoginView,
    //   meta: { requiresGuest: true },
    // },
    // {
    //   path: '/register',
    //   name: 'register',
    //   component: RegisterView,
    //   meta: { requiresGuest: true },
    // },
    // {
    //   path: '/forgot-password',
    //   name: 'forgot-password',
    //   component: ForgotPasswordView,
    //   meta: { requiresGuest: true },
    // },
    // {
    //   path: '/reset-password',
    //   name: 'reset-password',
    //   component: ResetPasswordView,
    //   meta: { requiresGuest: true },
    // },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { requiresAuth: true },
    },
    // Chuyển hướng mặc định
    {
      path: '/',
      redirect: '/dashboard',
    },
  ],
})

// Navigation Guard (giữ nguyên)
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
