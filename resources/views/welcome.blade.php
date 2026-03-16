<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Luxury Car Rental | Premium Fleet</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#fafafa] dark:bg-[#0a0a0a] text-[#1b1b18] min-h-screen font-sans">
        <!-- Hero Section -->
        <div class="relative h-screen flex items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="/images/hero.png" alt="Luxury Car" class="w-full h-full object-cover brightness-50">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-[#fafafa] dark:to-[#0a0a0a]"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 max-w-5xl mx-auto px-6 text-center space-y-8">
                <div class="inline-flex items-center px-4 py-2 rounded-full glass text-sm font-medium text-blue-600 dark:text-blue-400 mb-4 animate-bounce">
                    ✨ Nouvelle Collection 2026 est disponible
                </div>
                
                <h1 class="text-6xl md:text-8xl font-bold tracking-tight text-white mb-6">
                    L'Élégance en <span class="text-blue-500">Mouvement</span>
                </h1>
                
                <p class="text-xl md:text-2xl text-neutral-200 max-w-3xl mx-auto leading-relaxed">
                    Découvrez une expérience de conduite inégalée avec notre flotte de véhicules de prestige. Confort, performance et exclusivité à chaque kilomètre.
                </p>

                <div class="flex flex-col sm:flex-row gap-6 justify-center mt-12">
                    @if (Route::has('login'))
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary" size="base" class="px-8 py-4 text-lg rounded-full" wire:navigate>
                                Accéder au Tableau de Bord
                            </flux:button>
                        @else
                            <flux:button href="{{ route('login') }}" variant="primary" size="base" class="px-8 py-4 text-lg rounded-full shadow-lg" wire:navigate>
                                Commencer l'Expérience
                            </flux:button>
                            @if (Route::has('register'))
                                <flux:button href="{{ route('register') }}" variant="ghost" size="base" class="px-8 py-4 text-lg rounded-full text-white hover:bg-white/10" wire:navigate>
                                    Devenir Membre
                                </flux:button>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Stats/Features small bar -->
                <div class="grid grid-cols-3 gap-8 mt-24 max-w-2xl mx-auto border-t border-white/20 pt-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">50+</div>
                        <div class="text-sm text-neutral-400 uppercase tracking-widest">Modèles Luxe</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">24/7</div>
                        <div class="text-sm text-neutral-400 uppercase tracking-widest">Support Premium</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-white">100%</div>
                        <div class="text-sm text-neutral-400 uppercase tracking-widest">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="absolute bottom-6 w-full text-center text-neutral-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name') }} - Prestige Car Management
        </footer>
        
        @fluxScripts
    </body>
</html>
