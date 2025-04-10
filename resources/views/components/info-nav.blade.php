@props([
    'links' => [],
    'title' => 'Sur cette page',
    'showInfo' => false,
    'infoTitle' => 'Informations',
    'infoText' => '',
    'infoIcon' => null,
])

<aside {{ $attributes->merge(['class' => 'w-auto']) }}>
    <nav class="sticky top-4 w-44" id="sidebar-nav">
        @if (!empty($links))
            <div class="mb-6">
                <h2 class="font-bold mb-4 ml-4">{{ $title }}</h2>

                <ul class="space-y-1 mb-6 pl-4" id="sidebar-links">
                    @foreach ($links as $link)
                        <li>
                            <div class="relative">
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
        @endif

        @if ($showInfo)
            <div class="mb-6">
                <div role="alert" class="rounded-sm border-s-4 border-blue-500 bg-blue-50 p-4">
                    <div class="flex items-center gap-1 text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <strong class="block font-medium">{{ $infoTitle }}</strong>
                    </div>

                    <div class="flex flex-col gap-2 mt-2 text-sm text-blue-700">
                        @if ($infoText)
                            <p>
                                {{ $infoText }}
                                @if ($infoIcon)
                                    {!! $infoIcon !!}
                                @endif
                            </p>
                        @endif

                        {{ $info ?? '' }}
                    </div>
                </div>
            </div>
        @endif

        {{ $slot ?? '' }}
    </nav>
</aside>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sélectionner toutes les sections avec ID
                const sections = document.querySelectorAll('div[id]');
                const sidebarLinks = document.querySelectorAll('.sidebar-link');
                const indicators = document.querySelectorAll('.sidebar-indicator');

                // Initialiser la section active au chargement
                setInitialActiveSection();

                // Fonction pour définir la section active initiale
                function setInitialActiveSection() {
                    // Si nous avons un hash dans l'URL, l'utiliser
                    if (window.location.hash) {
                        const targetId = window.location.hash.substring(1);
                        highlightLink(targetId);
                    } else {
                        // Sinon, trouver la première section visible
                        checkActiveSection();
                    }
                }

                // Fonction pour vérifier quelle section est visible
                function checkActiveSection() {
                    const scrollPosition = window.scrollY + 100; // Offset pour une meilleure détection
                    let activeSection = null;

                    // Parcourir toutes les sections pour trouver celle qui est visible
                    sections.forEach(section => {
                        const sectionId = section.getAttribute('id');
                        // Vérifier si l'ID correspond à un lien dans la sidebar
                        const hasLink = Array.from(sidebarLinks).some(link =>
                            link.getAttribute('data-target') === sectionId
                        );

                        if (!hasLink) return; // Ignorer les sections sans lien correspondant

                        const sectionTop = section.offsetTop;
                        const sectionBottom = sectionTop + section.offsetHeight;

                        // Si la section est visible à l'écran
                        if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                            activeSection = sectionId;
                        }
                    });

                    // Si une section active a été trouvée, mettre à jour les liens
                    if (activeSection) {
                        highlightLink(activeSection);
                    }
                }

                // Fonction pour mettre en évidence le lien actif
                function highlightLink(sectionId) {
                    // Réinitialiser tous les liens et indicateurs
                    sidebarLinks.forEach(link => {
                        link.classList.remove('text-blue-600');
                        link.classList.add('text-gray-700');
                    });

                    indicators.forEach(indicator => {
                        indicator.classList.remove('bg-blue-600');
                        indicator.classList.add('bg-transparent');
                    });

                    // Mettre en évidence le lien actif
                    const activeLink = document.querySelector(`.sidebar-link[data-target="${sectionId}"]`);
                    const activeIndicator = document.querySelector(
                        `.sidebar-indicator[data-indicator-for="${sectionId}"]`);

                    if (activeLink) {
                        activeLink.classList.remove('text-gray-700');
                        activeLink.classList.add('text-blue-600');
                    }

                    if (activeIndicator) {
                        activeIndicator.classList.remove('bg-transparent');
                        activeIndicator.classList.add('bg-blue-600');
                    }
                }

                // Vérifier au défilement (avec throttling pour de meilleures performances)
                let isScrolling = false;
                window.addEventListener('scroll', function() {
                    if (!isScrolling) {
                        window.requestAnimationFrame(function() {
                            checkActiveSection();
                            isScrolling = false;
                        });
                        isScrolling = true;
                    }
                });

                // Gestion du clic sur les liens de la sidebar pour un défilement en douceur
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('data-target');
                        const targetElement = document.getElementById(targetId);

                        if (targetElement) {
                            // Mettre à jour l'URL avec le hash
                            history.pushState(null, null, `#${targetId}`);

                            // Défiler jusqu'à l'élément
                            window.scrollTo({
                                top: targetElement.offsetTop - 20,
                                behavior: 'smooth'
                            });

                            // Mettre à jour les liens actifs
                            highlightLink(targetId);
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
