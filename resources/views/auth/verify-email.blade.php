@extends('layouts.guest')

@section('title', 'Vérification de l\'adresse e-mail')

@section('content')
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-12">
        <div class="relative px-6 pt-10 pb-9 mx-auto w-full max-w-lg">
            <div class="mx-auto flex w-full max-w-md flex-col space-y-2">
                <div class="flex flex-col items-center justify-center text-center space-y-2">
                    <div class="font-semibold text-3xl">
                        <p>Vérifiez votre boîte mail</p>
                    </div>
                    <div class="flex flex-row text-sm font-medium text-gray-600">
                        <p>Cliquez sur le lien qui a été envoyé à votre adresse mail.</p>
                    </div>
                </div>

                <div>
                    <form action="{{ route('verification.send') }}" method="post">
                        <div
                            class="flex flex-row items-center justify-center text-center text-sm font-medium space-x-1 text-gray-500">
                            <p>Vous n'avez pas reçu le mail?</p> <a class="flex flex-row items-center text-blue-600"
                                href="http://" target="_blank" rel="noopener noreferrer">Renvoyer</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
