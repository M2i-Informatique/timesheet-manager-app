import axios, { AxiosInstance, AxiosRequestConfig } from 'axios'
import Cookies from 'js-cookie'
import { 
  AuthResponse, 
  LoginCredentials, 
  User, 
  Project, 
  TimesheetEntry, 
  MonthlyTimesheet,
  ApiResponse,
  PaginatedResponse,
  ApiError
} from '@/types'

class ApiClient {
  private client: AxiosInstance

  constructor() {
    this.client = axios.create({
      baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api',
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })

    // Request interceptor to add auth token
    this.client.interceptors.request.use(
      (config) => {
        const token = Cookies.get('auth_token')
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        return config
      },
      (error) => Promise.reject(error)
    )

    // Response interceptor to handle errors
    this.client.interceptors.response.use(
      (response) => response.data,
      (error) => {
        if (error.response?.status === 401) {
          // Clear auth data on 401
          Cookies.remove('auth_token')
          Cookies.remove('auth_user')
          window.location.href = '/login'
        }
        
        const apiError: ApiError = {
          message: error.response?.data?.message || error.message,
          errors: error.response?.data?.errors,
          status_code: error.response?.status,
        }
        
        return Promise.reject(apiError)
      }
    )
  }

  // Auth endpoints
  async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await this.client.post<any, ApiResponse<AuthResponse>>('/auth/login', credentials)
    return response.data
  }

  async logout(): Promise<void> {
    await this.client.post('/auth/logout')
    Cookies.remove('auth_token')
    Cookies.remove('auth_user')
  }

  async me(): Promise<User> {
    const response = await this.client.get<any, ApiResponse<User>>('/auth/me')
    return response.data
  }

  // Project endpoints
  async getProjects(params?: { page?: number; per_page?: number }): Promise<PaginatedResponse<Project>> {
    const response = await this.client.get<any, ApiResponse<PaginatedResponse<Project>>>('/projects', { params })
    return response.data
  }

  async getUserProjects(): Promise<Project[]> {
    const response = await this.client.get<any, ApiResponse<Project[]>>('/user/projects')
    return response.data
  }

  async getProject(id: number): Promise<Project> {
    const response = await this.client.get<any, ApiResponse<Project>>(`/projects/${id}`)
    return response.data
  }

  // Timesheet endpoints
  async getTimesheet(projectId: number, month: number, year: number): Promise<MonthlyTimesheet> {
    const response = await this.client.get<any, ApiResponse<MonthlyTimesheet>>(
      `/timesheets/${projectId}/${year}/${month}`
    )
    return response.data
  }

  async saveTimesheetEntry(entry: Partial<TimesheetEntry>): Promise<TimesheetEntry> {
    if (entry.id) {
      const response = await this.client.put<any, ApiResponse<TimesheetEntry>>(
        `/timesheet-entries/${entry.id}`, 
        entry
      )
      return response.data
    } else {
      const response = await this.client.post<any, ApiResponse<TimesheetEntry>>(
        '/timesheet-entries', 
        entry
      )
      return response.data
    }
  }

  async deleteTimesheetEntry(id: number): Promise<void> {
    await this.client.delete(`/timesheet-entries/${id}`)
  }

  // Export endpoints
  async exportTimesheet(projectId: number, month: number, year: number): Promise<Blob> {
    const response = await this.client.get(
      `/exports/timesheet/${projectId}/${year}/${month}`,
      { responseType: 'blob' }
    )
    return response as unknown as Blob
  }

  // Generic request method for custom endpoints
  async request<T = any>(config: AxiosRequestConfig): Promise<T> {
    const response = await this.client.request(config)
    return response.data
  }
}

export const apiClient = new ApiClient()
export default apiClient