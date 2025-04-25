<!-- resources/views/livewire/components/navigation-mobile.blade.php -->
<div>
    <div class="fixed bottom-4 left-0 z-50 w-full h-16 bg-transparent md:hidden">
        <div class="grid grid-cols-3 h-full rounded-xl font-medium bg-blue-400 m-2">
            <!-- Accueil -->
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="w-8 h-8 text-gray-100 group-hover:text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="text-sm text-gray-100 group-hover:text-blue-600">Accueil</span>
            </a>
            
            <!-- Pointage -->
            <a href="{{ route('tracking.index') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="w-8 h-8 text-gray-100 group-hover:text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="text-sm text-gray-100 group-hover:text-blue-600">Pointage</span>
            </a>
            
            <!-- Déconnexion -->
            <form action="{{ route('logout') }}" method="POST" class="flex items-center justify-center">
                @csrf
                <button type="submit" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group w-full h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                        class="w-8 h-8 text-gray-100 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span class="text-sm text-gray-100 group-hover:text-blue-600">Déconnexion</span>
                </button>
            </form>
        </div>
    </div>

    <style wire:ignore>
        @media (max-width: 768px) {
            body {
                padding-bottom: 5rem; /* Ajusté pour le nouveau style de navigation */
            }
        }
    </style>
</div>