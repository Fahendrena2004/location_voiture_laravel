@props(['voiture'])

<div x-show="(category === 'All' || category === '{{ $voiture->categorie }}') && 
             (seats === 'Tous' || seats === 'All' || (seats === '7+' ? {{ $voiture->nombre_places }} >= 7 : {{ $voiture->nombre_places }} == seats)) &&
             (search === '' || '{{ strtolower($voiture->marque . ' ' . $voiture->modele) }}'.includes(search.toLowerCase()))"
    x-transition
    class="group relative bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-zinc-100 dark:border-zinc-800 flex flex-col h-full">
    <!-- Image Section -->
    <div class="relative h-56 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
        <div
            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
        </div>

        <!-- Placeholder Image using Generate Image tool logic (simulated with a nice color/gradient + icon for now) -->
        <div
            class="w-full h-full flex items-center justify-center bg-gradient-to-br from-yolk-400/20 to-blue-500/20 group-hover:scale-110 transition-transform duration-700">
            <flux:icon name="truck" class="size-20 text-zinc-300 dark:text-zinc-700 opacity-50" />
        </div>

        <!-- Badge Status -->
        <div class="absolute top-4 right-4 z-20">
            @if($voiture->statut === 'disponible')
                <flux:badge color="green" size="sm" class="font-bold tracking-tight uppercase px-3 rounded-full">Disponible
                </flux:badge>
            @elseif($voiture->statut === 'loué')
                <flux:badge color="blue" size="sm" class="font-bold tracking-tight uppercase px-3 rounded-full">Loué
                </flux:badge>
            @else
                <flux:badge color="red" size="sm" class="font-bold tracking-tight uppercase px-3 rounded-full">Entretien
                </flux:badge>
            @endif
        </div>

        <!-- Price Over Image -->
        <div class="absolute bottom-4 left-4 z-20">
            <p class="text-white">
                <span
                    class="text-2xl font-black">{{ \App\Helpers\CurrencyHelper::format($voiture->prix_journalier) }}</span>
                <span class="text-xs opacity-80">/ jour</span>
            </p>
        </div>
    </div>

    <!-- Content Section -->
    <div class="p-6 flex flex-col flex-1">
        <div class="mb-4">
            <h3 class="text-xl font-black text-zinc-900 dark:text-white group-hover:text-yolk-600 transition-colors">
                {{ $voiture->marque }} {{ $voiture->modele }}
            </h3>
            <div class="flex items-center gap-2">
                <p class="text-sm text-zinc-500 dark:text-zinc-400 font-medium uppercase tracking-widest">
                    {{ $voiture->immatriculation }}
                </p>
                <flux:badge size="sm" variant="outline" class="text-[10px] font-black uppercase tracking-tighter">
                    {{ $voiture->categorie }}
                </flux:badge>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                <flux:icon name="user-group" class="size-4 opacity-50" />
                <span class="text-sm font-bold">{{ $voiture->nombre_places }} places</span>
            </div>
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                <flux:icon name="swatch" class="size-4 opacity-50" />
                <span class="text-sm font-bold capitalize">{{ $voiture->couleur }}</span>
            </div>
        </div>

        <flux:spacer />

        <div class="flex items-center gap-2 mt-auto">
            @if($voiture->statut === 'disponible')
                <flux:button as="a" href="{{ route('locations.create', ['voiture_id' => $voiture->id]) }}" variant="filled"
                    class="flex-1 bg-yolk-500 hover:bg-yolk-600 text-black font-black rounded-xl py-4 shadow-lg shadow-yolk-500/20">
                    Réserver maintenant
                </flux:button>
            @else
                <flux:button variant="subtle" class="flex-1 rounded-xl py-4 opacity-50 cursor-not-allowed" disabled>
                    Non disponible
                </flux:button>
            @endif

            <flux:button as="a" href="{{ route('voitures.show', $voiture) }}" icon="eye" variant="outline"
                class="rounded-xl p-4 border-zinc-200" />
        </div>
    </div>
</div>