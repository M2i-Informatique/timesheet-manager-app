// User & Auth Types
export interface User {
  id: number
  name: string
  email: string
  role: 'driver' | 'leader' | 'admin' | 'super-admin'
  company_id?: number
  created_at: string
  updated_at: string
}

export interface AuthUser extends User {
  permissions: string[]
}

export interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

export interface AuthResponse {
  user: AuthUser
  token: string
  expires_in: number
}

// Project Types
export interface Project {
  id: number
  code: string
  name: string
  description?: string
  zone_id: number
  zone: Zone
  category: 'MH' | 'GO' | 'Other'
  status: 'active' | 'inactive' | 'completed'
  created_at: string
  updated_at: string
}

// Zone Types
export interface Zone {
  id: number
  name: string
  distance: number
  rate_per_km: number
  created_at: string
  updated_at: string
}

// Worker Types
export interface Worker {
  id: number
  name: string
  email: string
  salary: number
  contract_hours: number
  type: 'employee' | 'interim'
  created_at: string
  updated_at: string
}

// Timesheet Types
export interface TimesheetEntry {
  id: number
  user_id: number
  project_id: number
  date: string
  hours: number
  category: 'day' | 'night'
  description?: string
  created_at: string
  updated_at: string
}

export interface DailyTimesheet {
  date: string
  entries: TimesheetEntry[]
  total_hours: number
}

export interface MonthlyTimesheet {
  month: number
  year: number
  project_id: number
  daily_entries: DailyTimesheet[]
  total_hours: number
  total_days: number
}

// API Response Types
export interface ApiResponse<T = any> {
  data: T
  message?: string
  status: 'success' | 'error'
}

export interface PaginatedResponse<T = any> {
  data: T[]
  current_page: number
  per_page: number
  total: number
  last_page: number
  from: number
  to: number
}

// Form Types
export interface TimesheetFormData {
  project_id: number
  month: number
  year: number
  category: 'day' | 'night'
}

export interface ProjectFormData {
  code: string
  name: string
  description?: string
  zone_id: number
  category: 'MH' | 'GO' | 'Other'
  status: 'active' | 'inactive' | 'completed'
}

// Error Types
export interface ApiError {
  message: string
  errors?: Record<string, string[]>
  status_code?: number
}

// Settings Types
export interface AppSettings {
  meal_allowance: number
  charge_rate: number
  company_name: string
  company_address: string
}