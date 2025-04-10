@extends('layouts.guest')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-12">
        <div class="relative px-6 pt-10 pb-9 mx-auto w-full max-w-lg">
            <div class="mx-auto flex w-full max-w-md flex-col space-y-2">
                <!-- Logo ou icône d'email -->
                <div class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-24 w-24 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>

                <div class="flex flex-col items-center justify-center text-center space-y-2 w-full">
                    <div class="font-semibold text-3xl">
                        <p>Mot de passe oublié ?</p>
                    </div>
                    <div class="flex flex-row text-sm font-semibold text-gray-800 mt-4">
                        <p class="w-2/3 mx-auto">Entrez votre adresse email et nous vous enverrons un lien pour
                            réinitialiser votre mot de passe.</p>
                    </div>
                </div>

                <!-- Message de statut (succès) -->
                @if (session('status'))
                    <x-message-flash title="Notification" type="success" :messages="[session('status')]">
                    </x-message-flash>
                @endif

                <!-- Message d'erreur -->
                @if ($errors->any())
                    <x-message-flash :messages="$errors" />
                @endif

                <div class="flex flex-col items-center justify-center mt-6 w-full">
                    <div class="w-2/3">
                        <form class="space-y-5" action="{{ route('password.email') }}" method="post">
                            @csrf

                            <x-forms.floating-input type="email" name="email" id="email" label="Votre email"
                                labelClass="bg-gray-50" autocomplete="email" :value="old('email')" :error="$errors->first('email')" required />

                            <div class="flex flex-col gap-4 items-center justify-center mt-6 w-full">
                                <div class="w-64">
                                    <x-button-dynamic tag="button" type="submit" class="w-full">
                                        Envoyer le lien de réinitialisation
                                    </x-button-dynamic>
                                </div>
                                <div class="w-64">
                                    <x-button-dynamic tag="a" route="login"
                                        class="w-full text-center">
                                        Retour à la connexion
                                    </x-button-dynamic>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
