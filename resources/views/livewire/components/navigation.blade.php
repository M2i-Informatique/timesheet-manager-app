{{-- resources/views/components/navigation.blade.php --}}

<div class="navigation-wrapper">
    {{-- Navigation desktop --}}
    <div class="hidden md:block">
        <nav class="futuristic-navbar">
            <div class="nav-background">
                <div class="nav-glow"></div>
            </div>

            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto">
                <div class="logo-container">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="logo-text">{{ config('app.name') }}</span>
                    </a>
                </div>

                <div class="items-center flex-grow px-8" id="navbar-cta">
                    @if (!request()->routeIs('admin.*'))
                    <ul class="menu-items flex gap-4">
                        @foreach ($links as $link)
                        <li class="h-full relative menu-item">
                            <x-links.nav
                                href="{{ route($link['route']) }}"
                                :active="request()->routeIs($link['route'])"
                                class="h-full py-4 menu-link {{ request()->routeIs($link['active_route']) ? 'active-link' : '' }}">
                                <span class="link-text">{{ $link['name'] }}</span>
                            </x-links.nav>
                            @if (request()->routeIs($link['active_route']))
                            <div class="active-indicator"></div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>

                <div class="flex space-x-3 rtl:space-x-reverse gap-2">
                    @if (request()->routeIs('admin.*'))
                    <x-button-dynamic tag="a" route="home" color="blue" class="nav-button home-button my-2">
                        <i class="fas fa-home mr-2"></i>Retour à l'accueil
                    </x-button-dynamic>
                    @endif

                    @if ((auth()->user()->hasRole('leader') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')) && !request()->routeIs('admin.*'))
                    <x-button-dynamic tag="a" route="admin.reporting.index" color="blue" class="nav-button dashboard-button my-2">
                        <i class="fas fa-chart-line mr-2"></i>Tableau de bord
                    </x-button-dynamic>
                    @endif

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <x-button-dynamic tag="button" type="submit" color="red" class="nav-button logout-button my-2">
                            <i class="fas fa-sign-out-alt mr-2"></i>Se déconnecter
                        </x-button-dynamic>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    {{-- Version mobile --}}
    <div class="block md:hidden">
        <nav class="futuristic-navbar-mobile">
            <div class="max-w-screen-xl flex items-center justify-between mx-auto p-4">
                <a href="{{ route('home') }}" class="flex items-center">
                    <span class="logo-text-mobile">{{ config('app.name') }}</span>
                </a>

                <button type="button" class="mobile-menu-button" id="mobileMenuButton">
                    <div class="hamburger-icon">
                        <span class="line line-1"></span>
                        <span class="line line-2"></span>
                        <span class="line line-3"></span>
                    </div>
                </button>
            </div>

            <div class="mobile-menu" id="mobileMenu">
                @if (!request()->routeIs('admin.*'))
                <ul class="mobile-menu-items">
                    @foreach ($links as $link)
                    <li class="mobile-menu-item {{ request()->routeIs($link['active_route']) ? 'active' : '' }}">
                        <a href="{{ route($link['route']) }}" class="mobile-menu-link">
                            {{ $link['name'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif

                <div class="mobile-buttons">
                    @if (request()->routeIs('admin.*'))
                    <a href="{{ route('home') }}" class="mobile-button home-button">
                        <i class="fas fa-home mr-2"></i>Retour à l'accueil
                    </a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="mobile-button logout-button w-full text-center">
                            <i class="fas fa-sign-out-alt mr-2"></i>Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <style>
        /* Style pour la barre de navigation futuriste */
        .futuristic-navbar {
            position: relative;
            background: linear-gradient(to right, #14213d, #1a2c4e);
            border-bottom: 1px solid rgba(58, 134, 255, 0.2);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 100;
        }

        /* Effets d'arrière-plan */
        .nav-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .nav-glow {
            position: absolute;
            top: -50px;
            left: -10%;
            width: 120%;
            height: 100px;
            background: linear-gradient(to bottom, rgba(58, 134, 255, 0.15), transparent);
            filter: blur(15px);
            animation: nav-pulse 8s ease-in-out infinite;
        }

        @keyframes nav-pulse {

            0%,
            100% {
                opacity: 0.5;
                transform: translateY(0);
            }

            50% {
                opacity: 0.8;
                transform: translateY(10px);
            }
        }

        /* Logo */
        .logo-container {
            position: relative;
            overflow: hidden;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f8f9fa;
            position: relative;
            display: inline-block;
            background: linear-gradient(90deg, #3a86ff, #8338ec, #ff006e);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: logo-shine 6s linear infinite;
        }

        @keyframes logo-shine {
            0% {
                background-position: 0% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        /* Menu items */
        .menu-items {
            display: flex;
            justify-content: center;
        }

        .menu-item {
            position: relative;
            overflow: hidden;
        }

        .menu-link {
            color: #f8f9fa;
            text-decoration: none;
            padding: 0.8rem 1.2rem;
            display: flex;
            align-items: center;
            position: relative;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .menu-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #3a86ff;
            transform: translateX(-50%);
            transition: width 0.3s ease;
            z-index: 1;
        }

        .menu-link:hover {
            color: #3a86ff;
        }

        .menu-link:hover::before {
            width: 80%;
        }

        .link-text {
            position: relative;
            z-index: 2;
        }

        .active-link {
            color: #3a86ff;
        }

        .active-indicator {
            position: absolute;
            bottom: 0;
            left: 10%;
            width: 80%;
            height: 2px;
            background: linear-gradient(to right, #3a86ff, #8338ec);
            animation: active-pulse 2s ease-in-out infinite;
        }

        @keyframes active-pulse {

            0%,
            100% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }
        }

        /* Buttons */
        .nav-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .nav-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }

        .nav-button:hover::before {
            left: 100%;
        }

        .nav-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Mobile Navigation */
        .futuristic-navbar-mobile {
            position: relative;
            background: linear-gradient(to right, #14213d, #1a2c4e);
            border-bottom: 1px solid rgba(58, 134, 255, 0.2);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .logo-text-mobile {
            font-size: 1.3rem;
            font-weight: 700;
            color: #f8f9fa;
            background: linear-gradient(90deg, #3a86ff, #8338ec, #ff006e);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mobile-menu-button {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            border: 1px solid rgba(58, 134, 255, 0.2);
        }

        .mobile-menu-button {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            border: 1px solid rgba(58, 134, 255, 0.2);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .mobile-menu-button.active {
            position: fixed;
            top: 16px;
            right: 16px;
            background: rgba(20, 33, 61, 0.95);
            box-shadow: 0 0 15px rgba(58, 134, 255, 0.3);
        }

        .hamburger-icon {
            width: 20px;
            height: 16px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .line {
            display: block;
            width: 100%;
            height: 2px;
            background-color: #f8f9fa;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Animation vers la croix */
        .mobile-menu-button.active .line-1 {
            transform: translateY(7px) rotate(45deg);
        }

        .mobile-menu-button.active .line-2 {
            opacity: 0;
            transform: translateX(-10px);
        }

        .mobile-menu-button.active .line-3 {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Animation au survol */
        .mobile-menu-button:hover .line-1 {
            width: 70%;
        }

        .mobile-menu-button:hover .line-3 {
            width: 70%;
            align-self: flex-end;
        }

        /* Pour éviter le conflit avec l'animation active */
        .mobile-menu-button.active:hover .line-1,
        .mobile-menu-button.active:hover .line-3 {
            width: 100%;
        }

        .mobile-menu {
            display: none;
            padding: 1rem;
            background: #14213d;
            border-top: 1px solid rgba(58, 134, 255, 0.1);
        }

        .mobile-menu.active {
            display: block;
        }

        .mobile-menu-items {
            list-style: none;
            padding: 0;
            margin: 0 0 1rem 0;
        }

        .mobile-menu-item {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .mobile-menu-link {
            display: block;
            padding: 0.8rem 0;
            color: #f8f9fa;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .mobile-menu-item.active .mobile-menu-link {
            color: #3a86ff;
        }

        .mobile-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .mobile-button {
            display: block;
            padding: 0.8rem 1rem;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .home-button,
        .dashboard-button {
            background: linear-gradient(135deg, #3a86ff, #8338ec);
            color: #f8f9fa;
        }

        .logout-button {
            background: linear-gradient(135deg, #ff006e, #ff5400);
            color: #f8f9fa;
        }

        /* Style pour l'effet de brillance sur les liens */
        .link-glow-effect {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(58, 134, 255, 0.3) 0%, transparent 70%);
            opacity: 0;
            z-index: 1;
            animation: link-glow 0.6s ease-out;
        }

        @keyframes link-glow {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: scale(1.5);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menu mobile toggle
            const menuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');

            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function() {
                    this.classList.toggle('active');
                    mobileMenu.classList.toggle('active');
                });
            }

            // Effet hover sur les liens du menu
            const menuLinks = document.querySelectorAll('.menu-link');

            menuLinks.forEach(link => {
                link.addEventListener('mouseover', function() {
                    // Effet de brillance
                    const glowEffect = document.createElement('div');
                    glowEffect.classList.add('link-glow-effect');
                    this.appendChild(glowEffect);

                    setTimeout(() => {
                        glowEffect.remove();
                    }, 600);
                });
            });
        });
    </script>
</div>