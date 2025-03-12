@extends('pages.admin.index')

@section('title', 'Gestion des utilisateurs')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestion des utilisateurs</h1>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Ajouter un utilisateur
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->first_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach ($user->roles as $role)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $role->name === 'super-admin'
                                    ? 'bg-red-100 text-red-800'
                                    : ($role->name === 'admin'
                                        ? 'bg-yellow-100 text-yellow-800'
                                        : 'bg-green-100 text-green-800') }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
