@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="flex justify-center items-center min-h-full">
    <div class="relative pt-12 pb-24 lg:py-24 w-full">
        <!-- Cercles animés en arrière-plan -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="circle-1"></div>
            <div class="circle-2"></div>
            <div class="circle-3"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <!-- Texte à gauche -->
            <div class="flex items-center">
                <div class="hero-content mb-12">
                    <div class="badge bg-blue-100 text-blue-800 mb-4 inline-flex items-center px-3 py-1 rounded-full animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-blue-700 mr-2"></span>
                        <span class="text-sm font-medium">Solution innovante</span>
                    </div>

                    <h1 class="mb-5 text-4xl font-bold !leading-tight text-dark sm:text-[42px] lg:text-[40px] xl:text-5xl animate-fadeIn">
                        <span class="text-customColor">Gérez</span> vos heures efficacement.
                    </h1>

                    <p class="mb-8 text-base text-body-color text-justify mr-4 leading-relaxed">
                        Simplifiez la gestion des heures de travail de vos équipes grâce à notre solution de pointage
                        <span class="relative inline-block">
                            <span class="text-customColor font-semibold">sur mesure</span>
                            <span class="absolute bottom-0 left-0 w-full h-[3px] bg-customColor opacity-30"></span>
                        </span>,
                        conçue pour répondre aux besoins spécifiques de chaque projet. Que vous soyez en déplacement ou au bureau,
                        suivez en temps réel les heures travaillées, gérez les chantiers et optimisez la productivité de vos salariés.
                    </p>

                    <div class="clients fade-in-up">
                        <h6 class="flex items-center mb-6 text-sm font-medium text-gray-600">
                            Votre partenaire informatique :
                            <span class="inline-block w-8 h-[2px] ml-3 bg-customColor"></span>
                        </h6>
                        <div class="partner-logos">
                            <a href="#" class="partner-logo">
                                <img src="/images/logo-m2i.png" alt="M2i" class="w-36 transition-all hover:scale-105" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image à droite avec motifs de points responsifs -->
            <div class="flex items-center justify-center">
                <div class="relative image-wrapper w-full max-w-full">
                    <!-- Flou gradient derrière l'image -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/20 to-purple-500/20 rounded-b-3xl rounded-tr-3xl -z-10 blur-xl transform -translate-y-4 translate-x-4 scale-95 opacity-70"></div>
                    
                    <!-- Image principale -->
                    <img src="{{ asset('/images/dubocq.png') }}" alt="Gestion des équipes sur chantier" class="w-full h-auto rounded-b-3xl rounded-tr-3xl shadow-xl img-hover" />
                    
                    <!-- Icône d'horloge animée -->
                    <div class="absolute -top-5 -right-2 bg-white rounded-full p-2 sm:p-3 shadow-lg animate-bounce-slow">
                        <div class="text-[#049ce3] p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-10 sm:w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Motif de points SVG - Coin supérieur gauche -->
                    <div class="absolute -left-2 sm:-left-4 md:-left-6 lg:-left-8 -top-2 sm:-top-4 md:-top-6 lg:-top-8 z-[-1] w-1/4 max-w-[100px] aspect-square">
                        <svg viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            @for ($x = 0; $x < 16; $x++)
                                @for ($y = 0; $y < 16; $y++)
                                    <circle cx="{{ 2.5 + $x * 24 }}" cy="{{ 2.5 + $y * 24 }}" r="2.5" fill="#049ce3" />
                                @endfor
                            @endfor
                        </svg>
                    </div>
                    
                    <!-- Motif de points SVG - Coin inférieur gauche -->
                    <div class="absolute -left-2 sm:-left-4 md:-left-6 lg:-left-8 -bottom-2 sm:-bottom-4 md:-bottom-6 lg:-bottom-8 z-[-1] w-1/4 max-w-[100px] aspect-square">
                        <svg viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            @for ($x = 0; $x < 16; $x++)
                                @for ($y = 0; $y < 16; $y++)
                                    <circle cx="{{ 2.5 + $x * 24 }}" cy="{{ 2.5 + $y * 24 }}" r="2.5" fill="#049ce3" />
                                @endfor
                            @endfor
                        </svg>
                    </div>
                    
                    <!-- Motif de points SVG - Coin inférieur droit -->
                    <div class="absolute -right-2 sm:-right-4 md:-right-6 lg:-right-8 -bottom-2 sm:-bottom-4 md:-bottom-6 lg:-bottom-8 z-[-1] w-1/4 max-w-[100px] aspect-square">
                        <svg viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            @for ($x = 0; $x < 16; $x++)
                                @for ($y = 0; $y < 16; $y++)
                                    <circle cx="{{ 2.5 + $x * 24 }}" cy="{{ 2.5 + $y * 24 }}" r="2.5" fill="#049ce3" />
                                @endfor
                            @endfor
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicateurs clés -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 stats-container cursor-pointer">
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">+40%</div>
                        <div class="stat-label">de productivité</div>
                        <p class="stat-desc">Grâce à la gestion en temps réel</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">100%</div>
                        <div class="stat-label">sécurisé</div>
                        <p class="stat-desc">Conforme aux normes de sécurité RGPD</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animations et styles avancés */
    .btn-primary {
        @apply inline-flex items-center justify-center py-3 px-8 bg-customColor text-white font-semibold rounded-lg transition-all shadow-md;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .btn-primary:hover {
        @apply transform -translate-y-1 shadow-lg;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
        z-index: -1;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-secondary {
        @apply inline-flex items-center justify-center py-3 px-8 bg-white text-customColor font-semibold rounded-lg transition-all border border-customColor;
    }

    .btn-secondary:hover {
        @apply bg-gray-50 shadow-md transform -translate-y-1;
    }

    /* Animations des cercles en arrière-plan */
    .circle-1,
    .circle-2,
    .circle-3 {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(to right, rgba(4, 156, 227, 0.1), rgba(131, 56, 236, 0.1));
        filter: blur(40px);
    }

    .circle-1 {
        width: 400px;
        height: 400px;
        top: -150px;
        right: -100px;
        animation: float 8s ease-in-out infinite;
    }

    .circle-2 {
        width: 300px;
        height: 300px;
        bottom: -100px;
        left: -150px;
        animation: float 10s ease-in-out infinite 1s;
    }

    .circle-3 {
        width: 200px;
        height: 200px;
        top: 40%;
        left: 40%;
        animation: pulse 15s ease-in-out infinite;
    }

    @keyframes float {
        0%,
        100% {
            transform: translateY(0) translateX(0);
        }

        50% {
            transform: translateY(-30px) translateX(20px);
        }
    }

    @keyframes pulse {
        0%,
        100% {
            transform: scale(1);
            opacity: 0.7;
        }

        50% {
            transform: scale(1.1);
            opacity: 1;
        }
    }

    .animate-bounce-slow {
        animation: bounce-slow 3s infinite;
    }

    @keyframes bounce-slow {
        0%,
        100% {
            transform: translateY(-10%);
            animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
        }

        50% {
            transform: translateY(0);
            animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Style pour les partenaires */
    .partner-logos {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .partner-logo {
        transition: all 0.3s ease;
        filter: grayscale(0.5);
    }

    .partner-logo:hover {
        filter: grayscale(0);
        transform: scale(1.05);
    }

    /* Style pour l'image */
    .image-wrapper {
        position: relative;
        transition: all 0.5s ease;
    }

    .img-hover {
        transition: all 0.5s ease;
        transform: perspective(1000px) rotateY(0) rotateX(0);
        will-change: transform;
    }

    .image-wrapper:hover .img-hover {
        transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Style pour les stats */
    .stats-container {
        position: relative;
        z-index: 10;
        transition: all 0.3s ease;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }

    .stat-item:hover {
        background-color: rgba(255, 255, 255, 0.9);
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        color: var(--custom-color, #049ce3);
        background-color: rgba(4, 156, 227, 0.1);
        padding: 1rem;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .stat-item:hover .stat-icon {
        background-color: var(--custom-color, #049ce3);
        color: white;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--custom-color, #049ce3);
    }

    .stat-label {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }

    .stat-desc {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    /* Animation fade-in */
    .fade-in-up {
        animation: fadeInUp 1s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des stats au scroll
        const statsContainer = document.querySelector('.stats-container');
        if (statsContainer) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-up');
                    }
                });
            }, {
                threshold: 0.2
            });

            observer.observe(statsContainer);
        }

        // Animation 3D pour l'image au mouvement de la souris
        const imageWrapper = document.querySelector('.image-wrapper');
        const image = document.querySelector('.img-hover');

        if (imageWrapper && image) {
            imageWrapper.addEventListener('mousemove', (e) => {
                const rect = imageWrapper.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateY = (x - centerX) / 20;
                const rotateX = (centerY - y) / 20;

                image.style.transform = `perspective(1000px) rotateY(${rotateY}deg) rotateX(${rotateX}deg)`;
            });

            imageWrapper.addEventListener('mouseleave', () => {
                image.style.transform = 'perspective(1000px) rotateY(0) rotateX(0)';
            });
        }
    });
</script>
@endsection