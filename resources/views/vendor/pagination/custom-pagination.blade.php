@if ($paginator->hasPages())
    <div class="flex justify-between items-center p-4 bg-gray-50">
        {{-- Information sur le nombre d'éléments --}}
        <div class="text-sm text-gray-500">
            @if ($paginator->total() > 0)
                <p>
                    Affichage de
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    @if ($paginator->firstItem() != $paginator->lastItem())
                        à <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @endif
                    sur <span class="font-medium">{{ $paginator->total() }}</span>
                    {{ $paginator->total() > 1 ? 'éléments' : 'élément' }}
                </p>
            @else
                <p>Aucun élément trouvé</p>
            @endif
        </div>

        {{-- Pagination --}}
        <nav aria-label="Pagination">
            <ul class="inline-flex -space-x-px text-sm">
                {{-- Bouton Précédent --}}
                @if ($paginator->onFirstPage())
                    <li>
                        <span
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-white border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Précédent</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->previousPageUrl() }}"
                            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 cursor-pointer">
                            Précédent
                        </a>
                    </li>
                @endif

                {{-- Numéros de page --}}
                @foreach ($elements as $element)
                    {{-- Séparateur --}}
                    @if (is_string($element))
                        <li>
                            <span
                                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Liens de page --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li>
                                    <span
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-blue-600 bg-blue-50 border border-gray-300 cursor-pointer">{{ $page }}</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}"
                                        class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 cursor-pointer">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Bouton Suivant --}}
                @if ($paginator->hasMorePages())
                    <li>
                        <a href="{{ $paginator->nextPageUrl() }}"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 cursor-pointer">
                            Suivant
                        </a>
                    </li>
                @else
                    <li>
                        <span
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-white border border-gray-300 rounded-e-lg cursor-not-allowed">Suivant</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
