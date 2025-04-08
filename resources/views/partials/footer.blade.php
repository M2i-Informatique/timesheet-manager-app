<footer class="bg-white">
    <div class="mx-auto max-w-screen-xl pt-16 pb-8 lg:pt-24">

        <div class="mt-16 border-t border-gray-100 pt-8 sm:flex sm:items-center sm:justify-between lg:mt-24">
            <ul class="flex flex-wrap justify-center gap-4 text-xs lg:justify-end">
                <li>
                    <a href="https://informatique-m2i.fr" targer="_blank"
                        class="text-gray-500 transition hover:opacity-75">&copy; M2i
                        Informatique</a>
                </li>
            </ul>

            <ul class="flex flex-wrap justify-center gap-4 text-xs lg:justify-end">
                <li>
                    @php
                    $guideLinks = [
                    'driver' => 'https://app-eu1.hubspot.com/guide-creator/g/VoaAqRlcgt',
                    'leader' => 'https://app-eu1.hubspot.com/guide-creator/g/J9185nhnRF',
                    'admin' => '#',
                    'super-admin' => '#',
                    ];

                    $guideLink = '#'; // Lien par défaut

                    if (auth()->check()) {
                    // Attribuer le lien en fonction du rôle
                    foreach ($guideLinks as $role => $link) {
                    if (auth()->user()->hasRole($role)) {
                    $guideLink = $link;
                    break; // Arrêter après avoir trouvé le premier rôle correspondant
                    }
                    }
                    }
                    @endphp

                    <a href="{{ $guideLink }}" target="_blank" class="text-gray-500 transition hover:opacity-75">
                        Guide d'utilisation
                    </a>
                </li>
            </ul>

            <ul class="mt-8 flex justify-center gap-6 sm:mt-0 lg:justify-end">
                <li>
                    <a href="mailto:lucas@informatique-m2i.fr" rel="noreferrer" target="_blank"
                        class="flex flex-wrap justify-center items-center gap-2 text-xs lg:justify-end text-gray-500 transition hover:opacity-75">
                        <span class="text-gray-500 transition hover:opacity-75">Contacter le support</span>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>

                    </a>
                </li>
            </ul>
        </div>
    </div>
</footer>