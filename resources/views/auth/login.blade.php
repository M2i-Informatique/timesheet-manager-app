@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="container mx-auto p-4">
            <h1 class="flex justify-center my-8 items-center text-3xl font-extrabold text-black">
                Dubocq
                <span class="bg-blue-100 text-blue-800 text-xl font-semibold me-2 px-2.5 py-0.5 rounded-sm ms-2">
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
                <div class="mb-5 relative">
                    <input type="email" id="email" name="email" autocomplete="email" placeholder=" " required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <label for="email"
                        class="bg-gray-50 absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                        Votre email
                    </label>
                </div>
                <div class="mb-5 relative">
                    <input type="password" id="password" name="password" autocomplete="current-password" placeholder=" "
                        required
                        class="block p-2.5 w-full text-sm text-gray-900 bg-transparent rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <label for="password"
                        class="bg-gray-50 absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">
                        Votre mot de passe
                    </label>
                </div>

                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Se souvenir de moi
                        </label>
                    </div>
                    <div class="flex justify-center">
                        <a href="/forgot-password" class="text-blue-700 text-sm hover:underline">Mot de passe oublié ?</a>
                    </div>
                </div>

                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
@endsection
