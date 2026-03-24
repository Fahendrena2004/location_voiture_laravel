<x-layouts::app :title="__('Voitures')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl" x-data="{
            view: 'grid',
            category: 'All',
            seats: 'Tous',
            search: '',
            cars: @js($voitures->map(fn($v) => ['categorie' => $v->categorie, 'nombre_places' => $v->nombre_places, 'search_str' => strtolower($v->marque . ' ' . $v->modele)])),
            get visibleCount() {
                return this.cars.filter(c =>
                    (this.category === 'All' || c.categorie === this.category) &&
                    (this.seats === 'Tous' || this.seats === 'All' || (this.seats === '7+' ? c.nombre_places >= 7 : c.nombre_places == this.seats)) &&
                    (this.search === '' || c.search_str.includes(this.search.toLowerCase()))
                ).length;
            }
        }" @filter-category.window="category = $event.detail" @filter-seats.window="seats = $event.detail">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:heading size="xl">Voitures</flux:heading>

                <!-- View Toggle -->
                <div class="flex items-center bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
                    <button @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-white dark:bg-zinc-700 shadow-sm text-yolk-600' : 'text-zinc-500 hover:text-zinc-700'"
                        class="p-1.5 rounded-md transition-all" title="Vue Grille">
                        <flux:icon name="squares-2x2" class="size-4" />
                    </button>
                    <button @click="view = 'table'"
                        :class="view === 'table' ? 'bg-white dark:bg-zinc-700 shadow-sm text-yolk-600' : 'text-zinc-500 hover:text-zinc-700'"
                        class="p-1.5 rounded-md transition-all" title="Vue Tableau">
                        <flux:icon name="queue-list" class="size-4" />
                    </button>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
                <flux:button href="{{ route('voitures.create') }}" wire:navigate icon="plus" variant="primary">Nouvelle
                    Voiture</flux:button>
            @endif
        </div>

        <!-- Filters & Search -->
        <div class="flex flex-col gap-6 bg-white dark:bg-zinc-900/50 p-6 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm"
            x-data="{ selectedCategory: 'All', selectedSeats: 'Tous' }">

            <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                <!-- Search Bar -->
                <div class="flex-1">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em] mb-2 block">
                        {{ __('Rechercher une voiture') }}
                    </flux:label>
                    <flux:input x-model="search" placeholder="Marque, modèle..." icon="magnifying-glass" clearable />
                </div>
            </div>

            <div class="h-px w-full bg-zinc-100 dark:bg-zinc-800"></div>

            <div class="flex flex-col md:flex-row md:items-center gap-6">

                <!-- Category Filter -->
                <div class="flex flex-col gap-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em]">
                        {{ __('Catégorie') }}
                    </flux:label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="cat in ['All', 'Berline', 'SUV', 'Citadine', 'Luxe', 'Utilitaire']" :key="cat">
                            <button @click="selectedCategory = cat; $dispatch('filter-category', cat)"
                                :class="selectedCategory === cat ? 'bg-yolk-500 text-black border-yolk-500 shadow-lg shadow-yolk-500/20' : 'bg-transparent text-zinc-500 border-zinc-200 dark:border-zinc-700 hover:border-yolk-500'"
                                class="px-4 py-1.5 text-xs font-bold rounded-full border transition-all duration-300"
                                x-text="cat"></button>
                        </template>
                    </div>
                </div>

                <div class="hidden md:block w-px h-10 bg-zinc-100 dark:bg-zinc-800"></div>

                <!-- Seats Filter -->
                <div class="flex flex-col gap-2">
                    <flux:label class="text-[10px] font-black uppercase text-zinc-400 tracking-[0.2em]">
                        {{ __('Nombre de places') }}
                    </flux:label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="sVal in ['Tous', '2', '4', '5', '7+']" :key="sVal">
                            <button @click="selectedSeats = sVal; $dispatch('filter-seats', sVal)"
                                :class="selectedSeats === sVal ? 'bg-yolk-500 text-black border-yolk-500 shadow-lg shadow-yolk-500/20' : 'bg-transparent text-zinc-500 border-zinc-200 dark:border-zinc-700 hover:border-yolk-500'"
                                class="px-4 py-1.5 text-xs font-bold rounded-full border transition-all duration-300"
                                x-text="sVal"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900"
                role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid View -->
        <div x-show="view === 'grid'" x-cloak class="flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 overflow-y-auto pb-8 custom-scrollbar"
                id="car-grid">
                @foreach($voitures as $voiture)
                    <x-car-card :voiture="$voiture" />
                @endforeach
            </div>
            <!-- Empty State -->
            <div x-show="visibleCount === 0" class="flex flex-col items-center justify-center py-24 text-center gap-4">
                <div class="w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <flux:icon name="magnifying-glass" class="size-10 text-zinc-300 dark:text-zinc-600" />
                </div>
                <div>
                    <p class="text-lg font-black text-zinc-900 dark:text-white">Aucun véhicule trouvé</p>
                    <p class="text-sm text-zinc-400 mt-1">Essayez de modifier vos filtres de recherche.</p>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div x-show="view === 'table'" x-cloak
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Marque</flux:table.column>
                    <flux:table.column>Modèle</flux:table.column>
                    <flux:table.column>Immatriculation</flux:table.column>
                    <flux:table.column>Places</flux:table.column>
                    <flux:table.column>Prix/Jour</flux:table.column>
                    <flux:table.column>Statut</flux:table.column>
                    @if(auth()->user()->isAdmin())
                        <flux:table.column>Actions</flux:table.column>
                    @else
                        <flux:table.column></flux:table.column>
                    @endif
                </flux:table.columns>

                <flux:table.rows x-cloak>
                    @foreach($voitures as $voiture)
                        <flux:table.row x-show="(category === 'All' || category === '{{ $voiture->categorie }}') && 
                                                    (seats === 'Tous' || seats === 'All' || (seats === '7+' ? {{ $voiture->nombre_places }} >= 7 : {{ $voiture->nombre_places }} == seats)) &&
                                                    (search === '' || '{{ strtolower($voiture->marque . ' ' . $voiture->modele) }}'.includes(search.toLowerCase())) &&
                                                    ({{ $voiture->prix_journalier }} <= maxPrice)" x-transition>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 200px;" title="{{ $voiture->marque }}">
                                    {{ $voiture->marque }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="truncate" style="max-width: 250px;" title="{{ $voiture->modele }}">
                                    {{ $voiture->modele }}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $voiture->immatriculation }}</flux:table.cell>
                            <flux:table.cell>{{ $voiture->nombre_places }}</flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\CurrencyHelper::format($voiture->prix_journalier) }} / jour
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-start">
                                    <flux:badge
                                        variant="{{ $voiture->statut === 'disponible' ? 'success' : ($voiture->statut === 'louée' ? 'warning' : 'danger') }}"
                                        inset="left">
                                        {{ ucfirst($voiture->statut) }}
                                    </flux:badge>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="outline" size="sm" icon="ellipsis-vertical"
                                        class="rounded-full" />

                                    <flux:menu>
                                        <flux:menu.item href="{{ route('voitures.show', $voiture) }}" wire:navigate
                                            icon="eye">Voir</flux:menu.item>

                                        @if($voiture->statut === 'disponible')
                                            <flux:menu.item
                                                href="{{ route('locations.create', ['voiture_id' => $voiture->id]) }}"
                                                wire:navigate icon="calendar-days">Réserver</flux:menu.item>
                                        @endif

                                        @if(auth()->user()->isAdmin())
                                            <flux:menu.item href="{{ route('voitures.edit', $voiture) }}" wire:navigate
                                                icon="pencil">Modifier</flux:menu.item>

                                            <flux:menu.separator />

                                            <form action="{{ route('voitures.destroy', $voiture) }}" method="POST"
                                                onsubmit="return confirm('Êtes-vous sûr ?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:menu.item type="submit" as="button" icon="trash" variant="danger">
                                                    Supprimer</flux:menu.item>
                                            </form>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>