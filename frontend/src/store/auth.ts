import { create } from 'zustand'
import { persist, createJSONStorage } from 'zustand/middleware'
import Cookies from 'js-cookie'
import { AuthUser, LoginCredentials } from '@/types'
import { apiClient } from '@/lib/api'

interface AuthState {
  user: AuthUser | null
  token: string | null
  isAuthenticated: boolean
  isLoading: boolean
  
  // Actions
  login: (credentials: LoginCredentials) => Promise<void>
  logout: () => Promise<void>
  checkAuth: () => Promise<void>
  setUser: (user: AuthUser) => void
  setToken: (token: string) => void
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      isLoading: false,

      login: async (credentials: LoginCredentials) => {
        try {
          set({ isLoading: true })
          
          const { user, token, expires_in } = await apiClient.login(credentials)
          
          // Set token in cookies with expiration
          const expiresDate = new Date(Date.now() + expires_in * 1000)
          Cookies.set('auth_token', token, { expires: expiresDate, sameSite: 'lax' })
          Cookies.set('auth_user', JSON.stringify(user), { expires: expiresDate, sameSite: 'lax' })
          
          set({
            user,
            token,
            isAuthenticated: true,
            isLoading: false,
          })
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      },

      logout: async () => {
        try {
          await apiClient.logout()
        } catch (error) {
          console.error('Logout error:', error)
        } finally {
          // Clear all auth data
          Cookies.remove('auth_token')
          Cookies.remove('auth_user')
          
          set({
            user: null,
            token: null,
            isAuthenticated: false,
            isLoading: false,
          })
        }
      },

      checkAuth: async () => {
        const token = Cookies.get('auth_token')
        const userCookie = Cookies.get('auth_user')
        
        if (!token || !userCookie) {
          set({ user: null, token: null, isAuthenticated: false })
          return
        }

        try {
          set({ isLoading: true })
          
          // Verify token is still valid
          const user = await apiClient.me()
          
          set({
            user,
            token,
            isAuthenticated: true,
            isLoading: false,
          })
        } catch (error) {
          console.error('Auth check failed:', error)
          
          // Clear invalid auth data
          Cookies.remove('auth_token')
          Cookies.remove('auth_user')
          
          set({
            user: null,
            token: null,
            isAuthenticated: false,
            isLoading: false,
          })
        }
      },

      setUser: (user: AuthUser) => {
        set({ user })
        Cookies.set('auth_user', JSON.stringify(user), { sameSite: 'lax' })
      },

      setToken: (token: string) => {
        set({ token })
        Cookies.set('auth_token', token, { sameSite: 'lax' })
      },
    }),
    {
      name: 'auth-storage',
      storage: createJSONStorage(() => ({
        getItem: (name) => {
          // Don't persist sensitive data in localStorage, use cookies instead
          return null
        },
        setItem: () => {
          // Don't persist sensitive data in localStorage
        },
        removeItem: () => {
          // Don't persist sensitive data in localStorage
        },
      })),
      partialize: (state) => ({
        // Don't persist any auth state in localStorage for security
      }),
    }
  )
)