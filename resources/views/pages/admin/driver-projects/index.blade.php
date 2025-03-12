@extends('pages.admin.index')

@section('title', 'Attribution des projets aux drivers')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Attribution des projets aux drivers</h1>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projets
                            assignés</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($drivers as $driver)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->first_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $driver->email }}</td>
                            <td class="px-6 py-4">
                                @if ($driver->projects->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($driver->projects as $project)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $project->name }} ({{ $project->code }})
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500">Aucun projet assigné</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.driver-projects.edit', $driver) }}"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Gérer les projets
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
