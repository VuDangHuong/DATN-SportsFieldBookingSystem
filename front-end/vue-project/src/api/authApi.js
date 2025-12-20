import axiosClient from './axiosClient'

export const authApi = {
  // Gọi tới: public function login(LoginRequest $request)
  login(payload) {
    return axiosClient.post('auth/login', payload)
  },

  // Gọi tới: public function logout(Request $request)
  logout() {
    return axiosClient.post('auth/logout')
  },
}
