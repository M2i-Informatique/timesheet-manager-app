'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import { useAuthStore } from '@/store/auth'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { useQuery } from '@tanstack/react-query'
import { apiClient } from '@/lib/api'
import { Project } from '@/types'
import { Calendar, Clock, Users, Building } from 'lucide-react'
import Link from 'next/link'

export default function TrackingPage() {
  const { isAuthenticated, user, checkAuth, logout } = useAuthStore()
  const router = useRouter()
  const [selectedProject, setSelectedProject] = useState<number | ''>('')
  const [selectedMonth, setSelectedMonth] = useState(new Date().getMonth() + 1)
  const [selectedYear, setSelectedYear] = useState(new Date().getFullYear())

  // Check authentication on mount
  useEffect(() => {
    checkAuth()
  }, [checkAuth])

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!isAuthenticated && user === null) {
      router.push('/login')
    }
  }, [isAuthenticated, user, router])

  // Fetch user projects
  const { data: projects, isLoading: loadingProjects } = useQuery({
    queryKey: ['user-projects'],
    queryFn: () => apiClient.getUserProjects(),
    enabled: isAuthenticated,
  })

  // Fetch all projects for admin users
  const { data: allProjectsData, isLoading: loadingAllProjects } = useQuery({
    queryKey: ['all-projects'],
    queryFn: () => apiClient.getProjects({ per_page: 50 }),
    enabled: isAuthenticated && user?.role === 'admin',
  })

  const handleTimesheetView = () => {
    if (!selectedProject) return
    
    const params = new URLSearchParams({
      project_id: selectedProject.toString(),
      month: selectedMonth.toString(),
      year: selectedYear.toString(),
      category: 'day'
    })
    
    router.push(`/tracking/timesheet?${params}`)
  }

  const handleLogout = async () => {
    try {
      await logout()
      router.push('/login')
    } catch (error) {
      console.error('Logout error:', error)
    }
  }

  if (!isAuthenticated || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="w-8 h-8 border-2 border-customColor border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
          <p>Chargement...</p>
        </div>
      </div>
    )
  }

  const months = [
    { value: 1, label: 'Janvier' }, { value: 2, label: 'Février' }, { value: 3, label: 'Mars' },
    { value: 4, label: 'Avril' }, { value: 5, label: 'Mai' }, { value: 6, label: 'Juin' },
    { value: 7, label: 'Juillet' }, { value: 8, label: 'Août' }, { value: 9, label: 'Septembre' },
    { value: 10, label: 'Octobre' }, { value: 11, label: 'Novembre' }, { value: 12, label: 'Décembre' }
  ]

  const years = Array.from({ length: 8 }, (_, i) => 2023 + i)

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 py-4">
          <div className="flex justify-between items-center">
            <div className="flex items-center space-x-4">
              <h1 className="text-2xl font-bold text-gray-800">Timesheet Manager</h1>
              <div className="hidden sm:block w-px h-6 bg-gray-300"></div>
              <span className="text-sm text-gray-600">Bonjour, {user.name}</span>
            </div>
            <div className="flex items-center space-x-4">
              <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                {user.role}
              </span>
              <Button variant="outline" size="sm" onClick={handleLogout}>
                Déconnexion
              </Button>
            </div>
          </div>
        </div>
      </header>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        {/* Welcome Section */}
        <div className="mb-8">
          <h2 className="text-3xl font-bold text-gray-800 mb-2">Pointage</h2>
          <p className="text-gray-600">
            Sélectionnez un chantier et une période pour gérer vos heures de travail.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-8">
            {/* Timesheet Selection */}
            <Card className="shadow-lg">
              <CardHeader>
                <div className="flex items-center space-x-3">
                  <div className="p-2 bg-blue-100 rounded-lg">
                    <Calendar className="w-6 h-6 text-customColor" />
                  </div>
                  <div>
                    <CardTitle>Saisie des heures</CardTitle>
                    <CardDescription>
                      Choisissez le chantier et la période pour votre pointage
                    </CardDescription>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {/* Project Selection */}
                  <div className="space-y-2">
                    <label htmlFor="project" className="text-sm font-medium text-gray-700">
                      Chantier
                    </label>
                    <select
                      id="project"
                      value={selectedProject}
                      onChange={(e) => setSelectedProject(e.target.value === '' ? '' : parseInt(e.target.value))}
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-customColor focus:border-transparent"
                    >
                      <option value="">Choisir un chantier</option>
                      {projects?.map((project: Project) => (
                        <option key={project.id} value={project.id}>
                          {project.code} - {project.name}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Month Selection */}
                  <div className="space-y-2">
                    <label htmlFor="month" className="text-sm font-medium text-gray-700">
                      Mois
                    </label>
                    <select
                      id="month"
                      value={selectedMonth}
                      onChange={(e) => setSelectedMonth(parseInt(e.target.value))}
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-customColor focus:border-transparent"
                    >
                      {months.map((month) => (
                        <option key={month.value} value={month.value}>
                          {month.label}
                        </option>
                      ))}
                    </select>
                  </div>

                  {/* Year Selection */}
                  <div className="space-y-2">
                    <label htmlFor="year" className="text-sm font-medium text-gray-700">
                      Année
                    </label>
                    <select
                      id="year"
                      value={selectedYear}
                      onChange={(e) => setSelectedYear(parseInt(e.target.value))}
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-customColor focus:border-transparent"
                    >
                      {years.map((year) => (
                        <option key={year} value={year}>
                          {year}
                        </option>
                      ))}
                    </select>
                  </div>
                </div>

                <div className="mt-6">
                  <Button
                    onClick={handleTimesheetView}
                    disabled={!selectedProject}
                    variant="custom"
                    size="lg"
                    className="w-full md:w-auto"
                  >
                    <Clock className="w-4 h-4 mr-2" />
                    Afficher le pointage
                  </Button>
                </div>
              </CardContent>
            </Card>

            {/* My Projects */}
            <Card className="shadow-lg">
              <CardHeader>
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-3">
                    <div className="p-2 bg-green-100 rounded-lg">
                      <Building className="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                      <CardTitle>Mes Chantiers</CardTitle>
                      <CardDescription>
                        Projets auxquels vous êtes assigné
                      </CardDescription>
                    </div>
                  </div>
                  <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {projects?.length || 0} projet(s)
                  </span>
                </div>
              </CardHeader>
              <CardContent>
                {loadingProjects ? (
                  <div className="text-center py-4">
                    <div className="w-6 h-6 border-2 border-customColor border-t-transparent rounded-full animate-spin mx-auto"></div>
                  </div>
                ) : projects && projects.length > 0 ? (
                  <div className="space-y-3">
                    {projects.map((project: Project) => (
                      <div key={project.id} className="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                          <div className="font-medium text-gray-800">
                            {project.code} - {project.name}
                          </div>
                          <div className="text-sm text-gray-600">
                            Zone: {project.zone.name} • Distance: {project.zone.distance}km
                          </div>
                        </div>
                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                          project.category === 'MH' ? 'bg-blue-100 text-blue-800' :
                          project.category === 'GO' ? 'bg-purple-100 text-purple-800' :
                          'bg-gray-100 text-gray-800'
                        }`}>
                          {project.category}
                        </span>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-gray-500 text-center py-4">
                    Aucun projet assigné
                  </p>
                )}
              </CardContent>
            </Card>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Quick Stats */}
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Statistiques</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-sm text-gray-600">Projets actifs</span>
                  <span className="font-semibold">{projects?.length || 0}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-gray-600">Mois en cours</span>
                  <span className="font-semibold">
                    {months.find(m => m.value === selectedMonth)?.label}
                  </span>
                </div>
              </CardContent>
            </Card>

            {/* Quick Actions */}
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Actions rapides</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <Button variant="outline" size="sm" className="w-full justify-start">
                  <Clock className="w-4 h-4 mr-2" />
                  Voir les rapports
                </Button>
                <Button variant="outline" size="sm" className="w-full justify-start">
                  <Users className="w-4 h-4 mr-2" />
                  Tous les projets
                </Button>
                {user?.role === 'admin' && (
                  <Button variant="outline" size="sm" className="w-full justify-start" asChild>
                    <Link href="/admin">
                      <Building className="w-4 h-4 mr-2" />
                      Administration
                    </Link>
                  </Button>
                )}
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  )
}