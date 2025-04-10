<nav class="bg-gray-50 border border-gray-100 hidden md:block">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto">
        <div class="flex space-x-3 rtl:space-x-reverse gap-2">
            @if (request()->routeIs('admin.*'))
            <x-button-dynamic tag="a" route="home" color="blue" class="my-2">
                Retour à l'accueil
            </x-button-dynamic>
            @endif

            @if ((auth()->user()->hasRole('leader') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')) && !request()->routeIs('admin.*'))
            <x-button-dynamic tag="a" route="admin.reporting.index" color="blue" class="my-2">
                Tableau de bord
            </x-button-dynamic>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-button-dynamic tag="button" type="submit" color="red" class="my-2">
                    Se déconnecter
                </x-button-dynamic>
            </form>
        </div>
        
        <div class="items-center" id="navbar-cta">
            @if (!request()->routeIs('admin.*'))
            <ul class="flex gap-4">
                @foreach ($links as $link)
                <li class="h-full relative">
                    <x-links.nav href="{{ route($link['route']) }}" :active="request()->routeIs($link['route'])" class="h-full py-4">
                        {{ $link['name'] }}
                    </x-links.nav>
                    @if (request()->routeIs($link['active_route']))
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600"></div>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</nav>