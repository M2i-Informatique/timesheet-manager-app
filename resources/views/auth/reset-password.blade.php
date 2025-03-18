@extends('layouts.guest')

@section('title', 'Réinitialisation du mot de passe')

@section('content')
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-12">
        <div class="relative px-6 pt-10 pb-9 mx-auto w-full max-w-lg">
            <div class="mx-auto flex w-full max-w-md flex-col space-y-2">
                <!-- Logo ou icône de cadenas -->
                <div class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-24 w-24 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>

                <div class="flex flex-col items-center justify-center text-center space-y-2">
                    <div class="font-semibold text-3xl">
                        <p>Réinitialisez votre mot de passe</p>
                    </div>
                    <div class="flex flex-row text-sm font-semibold text-gray-800 mt-4">
                        <p>Veuillez entrer votre adresse email et votre nouveau mot de passe.</p>
                    </div>
                </div>

                <!-- Message de statut (succès) -->
                @if (session('status'))
                    <x-alert-error title="Notification" type="success" :messages="[session('status')]">
                    </x-alert-error>
                @endif

                <!-- Message d'erreur -->
                @if ($errors->any())
                    <x-alert-error :messages="$errors" />
                @endif

                <div class="flex flex-col items-center justify-center mt-6 w-full">
                    <div class="w-full max-w-md">
                        <form class="space-y-5" action="{{ route('password.update') }}" method="post">
                            @csrf

                            <!-- Token caché obtenu depuis la requête -->
                            <input type="hidden" name="token" value="{{ request()->route('token') }}">

                            <x-forms.floating-input type="email" name="email" id="email" label="Votre email"
                                labelClass="bg-gray-50" autocomplete="email" :value="request()->get('email')" :error="$errors->first('email')" required />

                            <x-forms.floating-input type="password" name="password" id="password"
                                label="Nouveau mot de passe" labelClass="bg-gray-50" autocomplete="new-password"
                                :error="$errors->first('password')" required />

                            <x-forms.floating-input type="password" name="password_confirmation" id="password_confirmation"
                                label="Confirmer le mot de passe" labelClass="bg-gray-50" autocomplete="new-password"
                                :error="$errors->first('password_confirmation')" required />

                            <div class="flex flex-col gap-4 items-center justify-center mt-6 w-full">
                                <div class="w-64">
                                    <x-buttons.dynamic tag="button" type="submit" class="w-full">
                                        Réinitialiser le mot de passe
                                    </x-buttons.dynamic>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="{{ route('login') }}" class="text-blue-500 text-sm hover:underline">
                                        Retour à la page de connexion
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
