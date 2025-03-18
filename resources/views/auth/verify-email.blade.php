@extends('layouts.guest')

@section('title', 'Vérification de l\'adresse e-mail')

@section('content')
    @if (session('status') == 'verification-link-sent')
        <x-alert-error title="Lien de vérification envoyé" type="success" :messages="['Un nouveau lien de vérification a été envoyé à votre adresse email.']">
        </x-alert-error>
    @endif
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-12">
        <div class="relative px-6 pt-10 pb-9 mx-auto w-full max-w-lg">
            <div class="mx-auto flex w-full max-w-md flex-col space-y-2">
                <!-- Logo ou icône d'email -->
                <div class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-24 w-24 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                    </svg>
                </div>

                <div class="flex flex-col items-center justify-center text-center space-y-2">
                    <div class="font-semibold text-3xl">
                        <p>Vérifiez votre boîte mail</p>
                    </div>
                    <div class="flex flex-row text-sm font-semibold text-gray-800 mt-4">
                        <p>Un lien de vérification a été envoyé à votre adresse email.<br>Cliquez sur ce lien pour activer
                            votre compte.</p>
                    </div>
                </div>

                <!-- Conteneur des boutons avec classe w-64 pour une largeur fixe -->
                <div class="flex flex-col gap-4 items-center justify-center mt-6 w-full">
                    <!-- Conteneur à largeur fixe pour les deux formulaires -->
                    <div class="w-64">
                        <form action="{{ route('verification.send') }}" method="post" class="text-center mb-3">
                            @csrf
                            <p class="text-sm text-gray-600 mb-3">Vous n'avez pas reçu le mail?</p>
                            <x-buttons.dynamic tag="button" type="submit" class="w-full">
                                Renvoyer le lien de vérification
                            </x-buttons.dynamic>
                        </form>

                        <form method="POST" action="{{ route('logout') }}" class="text-center">
                            @csrf
                            <x-buttons.dynamic tag="button" type="submit" color="red" class="w-full">
                                Se déconnecter
                            </x-buttons.dynamic>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
