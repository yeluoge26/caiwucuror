import apiClient from './client'

export interface LoginRequest {
  username: string
  password: string
}

export interface LoginResponse {
  success: boolean
  data: {
    token: string
    user: {
      id: number
      username: string
      display_name: string
      role_key: string
      role_name: string
    }
  }
  message?: string
}

export interface User {
  id: number
  username: string
  display_name: string
  role_key: string
  role_name: string
}

export const authApi = {
  login: async (data: LoginRequest): Promise<LoginResponse> => {
    const response = await apiClient.post<LoginResponse>('?r=auth/login', data)
    if (response.success && response.data.token) {
      localStorage.setItem('token', response.data.token)
    }
    return response
  },

  logout: async (): Promise<void> => {
    await apiClient.post('?r=auth/logout')
    localStorage.removeItem('token')
  },

  me: async (): Promise<{ success: boolean; data: User }> => {
    return apiClient.get('?r=auth/me')
  },
}
