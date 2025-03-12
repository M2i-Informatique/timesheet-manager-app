@extends('pages.admin.index')

@section('title', 'Détail de l\'utilisateur')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Détail de l'utilisateur: {{ $user->name }}</h1>
            <div>
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">
                    Modifier
                </a>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                    Retour à la liste
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <h2 class="text-lg font-medium text-gray-900">Informations de base</h2>
                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">ID</p>
                        <p class="mt-1">{{ $user->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nom</p>
                        <p class="mt-1">{{ $user->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Prénom</p>
                        <p class="mt-1">{{ $user->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Date de création</p>
                        <p class="mt-1">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-lg font-medium text-gray-900">Rôles</h2>
                <div class="mt-2">
                    @foreach ($user->roles as $role)
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $role->name === 'super-admin'
                            ? 'bg-red-100 text-red-800'
                            : ($role->name === 'admin'
                                ? 'bg-yellow-100 text-yellow-800'
                                : 'bg-green-100 text-green-800') }}">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            @if ($user->hasRole('driver'))
                <div class="mt-6">
                    <h2 class="text-lg font-medium text-gray-900">Projets assignés</h2>
                    <div class="mt-2">
                        @if ($user->projects->count() > 0)
                            <ul class="list-disc pl-5">
                                @foreach ($user->projects as $project)
                                    <li>{{ $project->name }} ({{ $project->code }})</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500">Aucun projet assigné.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
