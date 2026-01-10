import { create } from 'zustand'
import { authApi, User } from '../api/auth'

interface AuthState {
  user: User | null
  token: string | null
  loading: boolean
  login: (username: string, password: string) => Promise<void>
  logout: () => Promise<void>
  checkAuth: () => Promise<void>
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: localStorage.getItem('token'),
  loading: false,

  login: async (username: string, password: string) => {
    set({ loading: true })
    try {
      const response = await authApi.login({ username, password })
      if (response.success && response.data) {
        set({
          user: response.data.user,
          token: response.data.token,
          loading: false,
        })
      } else {
        set({ loading: false })
        throw new Error('Login failed')
      }
    } catch (error) {
      set({ loading: false })
      throw error
    }
  },

  logout: async () => {
    await authApi.logout()
    set({ user: null, token: null })
  },

  checkAuth: async () => {
    const token = localStorage.getItem('token')
    if (!token) {
      set({ user: null, token: null, loading: false })
      return
    }

    set({ loading: true })
    try {
      const response = await authApi.me()
      if (response.success && response.data) {
        set({ user: response.data, token, loading: false })
      } else {
        localStorage.removeItem('token')
        set({ user: null, token: null, loading: false })
      }
    } catch (error) {
      localStorage.removeItem('token')
      set({ user: null, token: null, loading: false })
    }
  },
}))
