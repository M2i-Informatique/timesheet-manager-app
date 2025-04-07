<nav class="bg-gray-50 border border-gray-100">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto">
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse gap-2">

            @if (request()->routeIs('admin.*'))
            <x-buttons.dynamic tag="a" route="home" color="blue" class="my-2">
                Retour à l'accueil
            </x-buttons.dynamic>
            @endif

            @if ((auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')) && !request()->routeIs('admin.*'))
            <x-buttons.dynamic tag="a" route="admin.reporting.index" color="blue" class="my-2">
                Tableau de bord
            </x-buttons.dynamic>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-buttons.dynamic tag="button" type="submit" color="red" class="my-2">
                    Se déconnecter
                </x-buttons.dynamic>
            </form>

            <button data-collapse-toggle="navbar-cta" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                aria-controls="navbar-cta" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-cta">

            @if (!request()->routeIs('admin.*'))
            <ul class="flex gap-4">
                @foreach ($links as $link)
                <li class="h-full relative">
                    <x-links.nav href="{{ route($link['route']) }}" :active="request()->routeIs($link['route'])" class="h-full py-4">
                        {{ $link['name'] }}
                    </x-links.nav>
                    @if (request()->routeIs($link['active_route']))
                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-600 hidden md:block"></div>
                    @endif
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</nav>