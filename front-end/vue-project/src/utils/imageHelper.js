const BACKEND_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'
export const getAvatarUrl = (user) => {
  //User null hoặc undefined
  if (!user) {
    return 'https://ui-avatars.com/api/?name=User&background=random'
  }

  //Không có avatar -> Dùng UI Avatars
  if (!user.avatar) {
    const name = encodeURIComponent(user.name || 'User')
    return `https://ui-avatars.com/api/?name=${name}&background=random&color=fff`
  }

  //Avatar là link tuyệt đối (Google/Facebook/Link ảnh ngoài)
  if (user.avatar.startsWith('http')) {
    return user.avatar
  }

  //Ảnh lưu local (Laravel Storage)
  return `${BACKEND_URL}/storage/${user.avatar}`
}

//Hàm xử lý khi ảnh bị lỗi (404)
export const handleImageError = (event, userName) => {
  const name = encodeURIComponent(userName || 'User')
  event.target.src = `https://ui-avatars.com/api/?name=${name}&background=random&color=fff`
}
