@extends('pages.admin.index')

@section('title', 'Ajouter un jour non travaillé')

@section('admin-content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Ajouter un jour non travaillé</h1>
            <a href="{{ route('admin.non-working-days.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Retour à la liste
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <form action="{{ route('admin.non-working-days.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="date" name="date" value="{{ old('date') }}" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="type" name="type" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Sélectionnez un type</option>
                            @foreach(\App\Models\NonWorkingDay::TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6" id="comment-field" style="display: none;">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                        <textarea id="comment" name="comment" rows="3"
                            placeholder="Précisez la raison de la fermeture..."
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('comment') }}</textarea>
                    </div>

                    <script>
                        document.getElementById('type').addEventListener('change', function() {
                            const commentField = document.getElementById('comment-field');
                            const commentInput = document.getElementById('comment');
                            
                            if (this.value === 'Fermeture') {
                                commentField.style.display = 'block';
                            } else {
                                commentField.style.display = 'none';
                                commentInput.value = '';
                            }
                        });

                        // Vérifier au chargement de la page
                        document.addEventListener('DOMContentLoaded', function() {
                            const typeSelect = document.getElementById('type');
                            if (typeSelect.value === 'Fermeture') {
                                document.getElementById('comment-field').style.display = 'block';
                            }
                        });
                    </script>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            <i class="fas fa-save mr-2"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
