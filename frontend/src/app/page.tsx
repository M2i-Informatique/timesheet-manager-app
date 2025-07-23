'use client'

import { useEffect } from 'react'
import { useAuthStore } from '@/store/auth'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import Link from 'next/link'
import { Clock, Shield, TrendingUp } from 'lucide-react'

export default function HomePage() {
  const { isAuthenticated, checkAuth } = useAuthStore()

  useEffect(() => {
    checkAuth()
  }, [checkAuth])

  if (isAuthenticated) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50">
        <div className="text-center space-y-6">
          <h1 className="text-4xl font-bold text-gray-800">
            Bienvenue dans <span className="text-customColor">Timesheet Manager</span>
          </h1>
          <p className="text-lg text-gray-600 max-w-2xl mx-auto">
            Gérez efficacement vos heures de travail et suivez vos projets en temps réel.
          </p>
          <div className="flex gap-4 justify-center">
            <Button asChild variant="custom" size="lg">
              <Link href="/tracking">
                <Clock className="w-5 h-5 mr-2" />
                Pointage
              </Link>
            </Button>
            <Button asChild variant="outline" size="lg">
              <Link href="/admin">
                Administration
              </Link>
            </Button>
          </div>
        </div>
      </div>
    )
  }

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative py-24 px-4 bg-gradient-to-br from-blue-50 to-purple-50 overflow-hidden">
        {/* Animated background circles */}
        <div className="absolute inset-0 overflow-hidden pointer-events-none">
          <div className="absolute w-96 h-96 -top-48 -right-24 bg-blue-200 rounded-full opacity-30 animate-float"></div>
          <div className="absolute w-72 h-72 -bottom-36 -left-36 bg-purple-200 rounded-full opacity-30 animate-float" style={{ animationDelay: '1s' }}></div>
        </div>

        <div className="max-w-7xl mx-auto relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            {/* Content */}
            <div>
              <div className="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium mb-6">
                <div className="w-2 h-2 bg-blue-600 rounded-full mr-2"></div>
                Solution innovante
              </div>
              
              <h1 className="text-4xl lg:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                <span className="text-customColor">Gérez</span> vos heures efficacement.
              </h1>
              
              <p className="text-lg text-gray-600 mb-8 leading-relaxed">
                Simplifiez la gestion des heures de travail de vos équipes grâce à notre solution de pointage{' '}
                <span className="text-customColor font-semibold relative">
                  sur mesure
                  <span className="absolute bottom-0 left-0 w-full h-1 bg-customColor opacity-30"></span>
                </span>
                , conçue pour répondre aux besoins spécifiques de chaque projet.
              </p>

              <div className="flex flex-col sm:flex-row gap-4 mb-12">
                <Button asChild variant="custom" size="lg" className="shadow-lg hover:shadow-xl transition-all">
                  <Link href="/login">
                    Commencer maintenant
                  </Link>
                </Button>
                <Button asChild variant="outline" size="lg">
                  <Link href="/demo">
                    Voir la démonstration
                  </Link>
                </Button>
              </div>

              <div className="flex items-center text-sm text-gray-600">
                <span>Votre partenaire informatique :</span>
                <span className="w-8 h-0.5 bg-customColor mx-3"></span>
                <img src="/images/placeholder-logo.svg" alt="M2i" className="h-8" />
              </div>
            </div>

            {/* Image */}
            <div className="relative">
              <div className="relative bg-white rounded-3xl shadow-2xl overflow-hidden">
                <img 
                  src="/images/placeholder-image.svg" 
                  alt="Gestion des équipes sur chantier" 
                  className="w-full h-auto"
                />
                <div className="absolute -top-4 -right-4 bg-white rounded-full p-3 shadow-lg animate-bounce-slow">
                  <Clock className="w-8 h-8 text-customColor" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 px-4">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <h2 className="text-3xl font-bold text-gray-800 mb-4">
              Pourquoi choisir notre solution ?
            </h2>
            <p className="text-lg text-gray-600 max-w-2xl mx-auto">
              Des fonctionnalités conçues pour optimiser votre productivité
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <Card className="text-center hover:shadow-lg transition-all group">
              <CardHeader>
                <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-customColor group-hover:text-white transition-all">
                  <TrendingUp className="w-8 h-8" />
                </div>
                <CardTitle className="text-2xl font-bold text-customColor">+40%</CardTitle>
                <CardDescription className="text-lg font-semibold">de productivité</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600">Grâce à la gestion en temps réel</p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-lg transition-all group">
              <CardHeader>
                <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-customColor group-hover:text-white transition-all">
                  <Shield className="w-8 h-8" />
                </div>
                <CardTitle className="text-2xl font-bold text-customColor">100%</CardTitle>
                <CardDescription className="text-lg font-semibold">sécurisé</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600">Conforme aux normes de sécurité RGPD</p>
              </CardContent>
            </Card>

            <Card className="text-center hover:shadow-lg transition-all group">
              <CardHeader>
                <div className="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-customColor group-hover:text-white transition-all">
                  <Clock className="w-8 h-8" />
                </div>
                <CardTitle className="text-2xl font-bold text-customColor">24/7</CardTitle>
                <CardDescription className="text-lg font-semibold">disponible</CardDescription>
              </CardHeader>
              <CardContent>
                <p className="text-gray-600">Accès depuis n&apos;importe où, à tout moment</p>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 px-4 bg-gray-50">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-3xl font-bold text-gray-800 mb-6">
            Prêt à optimiser votre gestion du temps ?
          </h2>
          <p className="text-lg text-gray-600 mb-8">
            Rejoignez les entreprises qui font confiance à notre solution
          </p>
          <Button asChild variant="custom" size="lg" className="shadow-lg hover:shadow-xl transition-all">
            <Link href="/login">
              Commencer gratuitement
            </Link>
          </Button>
        </div>
      </section>
    </div>
  )
}