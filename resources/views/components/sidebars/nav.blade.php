<aside class="w-auto">
    <nav class="sticky top-4" id="sidebar-nav">
        <div class="mb-6">
            <h2 class="font-bold mb-4 ml-4">Sur cette page</h2>

            <ul class="space-y-1 mb-6 pl-4" id="sidebar-links">
                @foreach ($links as $link)
                    <li>
                        <!-- Ajout d'une div conteneur avec position relative -->
                        <div class="relative">
                            <!-- Bordure inactive par défaut (transparente) -->
                            <span
                                class="absolute left-[-16px] h-full w-[2px] bg-transparent transition-colors duration-200 sidebar-indicator"
                                data-indicator-for="{{ $link['id'] }}"></span>

                            <a href="#{{ $link['id'] }}"
                                class="sidebar-link text-sm text-gray-700 block transition-colors duration-200"
                                data-target="{{ $link['id'] }}">
                                {{ $link['text'] }}
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>

        </div>
        <div class="mb-6">

            <h3 class="font-bold mb-4 ml-4">Informations</h3>

            <p class="w-38 pl-4 text-sm">
                Cliquez sur
                <svg class="w-4 h-4 inline-block align-center text-blue-600 " xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                </svg>
                pour télécharger une feuille de pointage vierge.
            </p>
            
        </div>
    </nav>
</aside>
