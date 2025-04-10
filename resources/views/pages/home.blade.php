@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<!-- <div class="h-full flex flex-col items-center justify-center">
    <h1 class="text-5xl text-black font-bold mb-8 animate-pulse">
        Votre page d'accueil bientôt disponible
    </h1>
    </div> -->
<div class="flex justify-center items-center min-h-full">
    <div class="relative pt-8 pb-24 lg:py-24">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Texte à gauche -->
            <div class="flex items-center">
                <div class="hero-content">
                    <h1 class="mb-5 text-4xl font-bold !leading-tight text-dark sm:text-[42px] lg:text-[40px] xl:text-5xl">
                        Dubocq <span class="text-customColor">Pointage</span> <br>
                        Gérez vos heures efficacement.
                    </h1>
                    <p class="mb-8 text-base text-body-color">
                        Simplifiez la gestion des heures de travail de vos équipes grâce à notre solution de pointage sur mesure, conçue pour répondre aux besoins spécifiques de chaque projet. Que vous soyez en déplacement ou au bureau, suivez en temps réel les heures travaillées, gérez les chantiers et optimisez la productivité de vos salariés.
                    </p>
                    <div class="mb-10">
                        <a href="#contact" class="inline-flex items-center justify-center py-3 px-8 bg-customColor text-white font-semibold rounded-lg transition-all hover:bg-opacity-90">
                            Demander une démonstration
                        </a>
                    </div>

                    <div class="clients">
                        <h6 class="flex items-center mb-6 text-sm font-normal text-body-color">
                            Votre partenaire informatique :
                            <span class="inline-block w-8 h-px ml-3 bg-body-color"></span>
                        </h6>
                        <div class="flex items-center gap-4 xl:gap-[50px]">
                            <a href="#" class="block py-3">
                                <img src="/images/logo-m2i.png" alt="M2i" class="w-36 logo-img" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Image à droite -->
            <div class="flex items-center justify-center">
                <div class="relative">
                    <img src="/images/constructors.jpg" alt="hero" class="max-w-full rounded-b-3xl rounded-tr-3xl shadow-xl" />
                    <span class="absolute -left-8 -top-8 z-[-1]">
                        <svg width="93" height="93" viewBox="0 0 93 93" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="2.5" cy="2.5" r="2.5" fill="#049ce3" />
                            <circle cx="2.5" cy="24.5" r="2.5" fill="#049ce3" />
                            <circle cx="2.5" cy="46.5" r="2.5" fill="#049ce3" />
                            <circle cx="2.5" cy="68.5" r="2.5" fill="#049ce3" />
                            <circle cx="2.5" cy="90.5" r="2.5" fill="#049ce3" />
                            <circle cx="24.5" cy="2.5" r="2.5" fill="#049ce3" />
                            <circle cx="24.5" cy="24.5" r="2.5" fill="#049ce3" />
                            <circle cx="24.5" cy="46.5" r="2.5" fill="#049ce3" />
                            <circle cx="24.5" cy="68.5" r="2.5" fill="#049ce3" />
                            <circle cx="24.5" cy="90.5" r="2.5" fill="#049ce3" />
                            <circle cx="46.5" cy="2.5" r="2.5" fill="#049ce3" />
                            <circle cx="46.5" cy="24.5" r="2.5" fill="#049ce3" />
                            <circle cx="46.5" cy="46.5" r="2.5" fill="#049ce3" />
                            <circle cx="46.5" cy="68.5" r="2.5" fill="#049ce3" />
                            <circle cx="46.5" cy="90.5" r="2.5" fill="#049ce3" />
                            <circle cx="68.5" cy="2.5" r="2.5" fill="#049ce3" />
                            <circle cx="68.5" cy="24.5" r="2.5" fill="#049ce3" />
                            <circle cx="68.5" cy="46.5" r="2.5" fill="#049ce3" />
                            <circle cx="68.5" cy="68.5" r="2.5" fill="#049ce3" />
                            <circle cx="68.5" cy="90.5" r="2.5" fill="#049ce3" />
                            <circle cx="90.5" cy="2.5" r="2.5" fill="#049ce3" />
                            <circle cx="90.5" cy="24.5" r="2.5" fill="#049ce3" />
                            <circle cx="90.5" cy="46.5" r="2.5" fill="#049ce3" />
                            <circle cx="90.5" cy="68.5" r="2.5" fill="#049ce3" />
                            <circle cx="90.5" cy="90.5" r="2.5" fill="#049ce3" />
                        </svg>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection