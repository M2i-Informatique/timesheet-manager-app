@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto p-4">
            <h1 class="flex justify-center my-8 items-center text-3xl font-extrabold text-black">
                Dubocq
                <span class="bg-blue-100 text-blue-500 text-lg font-semibold me-2 px-2.5 py-0.5 rounded-sm ms-2">
                    POINTAGE
                </span>
                <small class="text-xl ms-2 font-semibold text-gray-400">| Connectez-vous</small>
            </h1>

            <!-- Message d'erreur (initialement caché) -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 max-w-sm mx-auto"
                    role="alert">
                    @foreach ($errors->all() as $error)
                        <span class="block sm:inline">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form class="max-w-sm mx-auto" action="{{ route('login') }}" method="post">
                @csrf

                <x-forms.floating-input type="email" name="email" id="email" label="Votre email"
                    autocomplete="email" :value="old('email')" :error="$errors->first('email')" required />

                <x-forms.floating-input type="password" name="password" id="password" label="Votre mot de passe"
                    autocomplete="current-password" :error="$errors->first('password')" required />

                <div class="flex items-center justify-between mb-5">
                    <x-forms.checkbox id="remember" name="remember" label="Se souvenir de moi" :checked="old('remember')" />
                    <div class="flex justify-center">
                        <a href="/forgot-password" class="text-blue-500 text-sm hover:underline">Mot de passe oublié ?</a>
                    </div>
                </div>

                <x-buttons.dynamic tag="button" type="submit" color="blue" class="w-full bg-blue-500 text-white">
                    Se connecter
                </x-buttons.dynamic>
            </form>
        </div>
    </div>
@endsection
