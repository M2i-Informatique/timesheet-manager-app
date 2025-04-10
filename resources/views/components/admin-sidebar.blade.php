<!-- resources/views/layouts/admin-sidebar.blade.php -->
<aside class="w-64 fixed top-0 left-0 h-full z-10 bg-white border-r border-gray-200 shadow-sm overflow-y-auto">
    <div class="h-full flex flex-col overflow-y-auto">
        <!-- Logo et nom de l'application -->
        <div class="p-6 border-b border-gray-200">
            <a href="{{ route('admin.reporting.index') }}" class="flex items-center">
                <div class="h-8 w-full bg-blue-600 rounded flex items-center justify-center text-white font-bold mr-3">
                    Dubocq Pointage
                </div>
            </a>
        </div>

        <!-- Navigation principale -->
        <div class="flex-1 py-4 px-3 overflow-y-auto">
            <!-- Tableau de bord -->
            @if (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('leader'))
            <a href="{{ route('admin.reporting.index') }}"
                class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                   {{ request()->routeIs('admin.reporting.*')
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                <x-icons.chart class="w-4 h-4 mr-3 flex-shrink-0" />
                <span>Tableau de bord</span>
            </a>
            @endif

            <!-- Section Administration -->
            @if (auth()->user()->hasRole('super-admin'))
            <div class="mt-5">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Administration
                </h3>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                       {{ request()->routeIs('admin.users.*')
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                    <x-icons.users class="w-4 h-4 mr-3 flex-shrink-0" />
                    <span>Utilisateurs</span>
                </a>
            </div>
            @endif

            <!-- Section Ressources -->
            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
            <div class="mt-5">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Ressources
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('admin.workers.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.workers.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.employee class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Salariés</span>
                    </a>
                    <a href="{{ route('admin.interims.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.interims.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.employee class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Intérimaires</span>
                    </a>
                </div>
            </div>

            <!-- Section Projets -->
            <div class="mt-5">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Chantier
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('admin.projects.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.projects.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.electrical class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Chantiers</span>
                    </a>
                    <a href="{{ route('admin.zones.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.zones.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.map class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Zones</span>
                    </a>
                </div>
            </div>

            <!-- Section Configuration -->
            <div class="mt-5">
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    Configuration
                </h3>
                <div class="space-y-1">
                    <a href="{{ route('admin.non-working-days.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.non-working-days.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.calendar class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Jours de fermeture</span>
                    </a>
                    <a href="{{ route('admin.exports.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.exports.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.export class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Exporter</span>
                    </a>
                    @if (auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('admin.settings.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-md transition-colors
                           {{ request()->routeIs('admin.settings.*')
                                ? 'bg-blue-50 text-blue-700'
                                : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        <x-icons.setting class="w-4 h-4 mr-3 flex-shrink-0" />
                        <span>Paramétrages</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Boutons de navigation et déconnexion -->
        <div class="p-4 border-t border-gray-200 mt-auto">
            <div class="space-y-3">
                <x-button-dynamic tag="a" route="home" color="blue" class="w-full text-center">
                    Retour à l'accueil
                </x-button-dynamic>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <x-button-dynamic tag="button" type="submit" color="red" class="w-full">
                        Se déconnecter
                    </x-button-dynamic>
                </form>
            </div>
        </div>
    </div>
</aside>