<x-layouts::app :title="__('Détails') . ' - ' . $voiture->marque . ' ' . $voiture->modele">
    <div class="max-w-4xl mx-auto flex flex-col gap-8 pb-12">
        <!-- Header & Navigation -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('voitures.index') }}" wire:navigate icon="chevron-left" variant="ghost"
                    class="rounded-full" />
                <div>
                    <flux:heading size="xl">{{ $voiture->marque }} {{ $voiture->modele }}</flux:heading>
                    <p class="text-sm text-zinc-500 uppercase tracking-widest font-medium">
                        {{ $voiture->immatriculation }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(auth()->user()->isAdmin())
                    <flux:button href="{{ route('voitures.edit', $voiture) }}" wire:navigate icon="pencil"
                        variant="outline">Modifier</flux:button>
                @endif

                @if($voiture->statut === 'disponible')
                    <flux:button href="{{ route('locations.create', ['voiture_id' => $voiture->id]) }}" wire:navigate
                        variant="primary" icon="calendar-days">Réserver</flux:button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Image & Status -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <!-- Main Image Card -->
                <div
                    class="bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden border border-zinc-100 dark:border-zinc-800 shadow-sm relative aspect-video group">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-yolk-400/20 to-blue-500/20 flex items-center justify-center">
                        <flux:icon name="truck" class="size-32 text-zinc-200 dark:text-zinc-800" />
                    </div>

                    <div class="absolute top-6 right-6">
                        <flux:badge
                            variant="{{ $voiture->statut === 'disponible' ? 'success' : ($voiture->statut === 'louée' ? 'warning' : 'danger') }}"
                            class="text-xs font-black uppercase tracking-tight px-4 py-1.5 rounded-full shadow-lg">
                            {{ ucfirst($voiture->statut) }}
                        </flux:badge>
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div
                        class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex flex-col items-center justify-center gap-2 text-center">
                        <flux:icon name="user-group" class="size-6 text-yolk-500" />
                        <span class="text-xs text-zinc-400 font-bold uppercase tracking-tighter">Places</span>
                        <span class="font-black text-lg">{{ $voiture->nombre_places }}</span>
                    </div>
                    <div
                        class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex flex-col items-center justify-center gap-2 text-center">
                        <flux:icon name="swatch" class="size-6 text-yolk-500" />
                        <span class="text-xs text-zinc-400 font-bold uppercase tracking-tighter">Couleur</span>
                        <span class="font-black text-lg capitalize">{{ $voiture->couleur ?? 'N/A' }}</span>
                    </div>
                    <div
                        class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex flex-col items-center justify-center gap-2 text-center">
                        <flux:icon name="tag" class="size-6 text-yolk-500" />
                        <span class="text-xs text-zinc-400 font-bold uppercase tracking-tighter">Catégorie</span>
                        <span class="font-black text-lg">{{ $voiture->categorie }}</span>
                    </div>
                    <div
                        class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex flex-col items-center justify-center gap-2 text-center">
                        <flux:icon name="banknotes" class="size-6 text-yolk-500" />
                        <span class="text-xs text-zinc-400 font-bold uppercase tracking-tighter">Prix/Jour</span>
                        <span
                            class="font-black text-lg text-yolk-600">{{ \App\Helpers\CurrencyHelper::format($voiture->prix_journalier) }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Actions -->
            <div class="flex flex-col gap-6">
                <div
                    class="bg-white dark:bg-zinc-900 p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm space-y-6">
                    <flux:heading size="lg">Informations</flux:heading>

                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-50 dark:border-zinc-800/50">
                            <span class="text-zinc-500 text-sm">Immatriculation</span>
                            <span class="font-bold text-zinc-900 dark:text-white">{{ $voiture->immatriculation }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-50 dark:border-zinc-800/50">
                            <span class="text-zinc-500 text-sm">Prix journalier</span>
                            <span
                                class="font-black text-yolk-600">{{ \App\Helpers\CurrencyHelper::format($voiture->prix_journalier) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-zinc-500 text-sm">Dernière mise à jour</span>
                            <span class="text-xs text-zinc-400">{{ $voiture->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if($voiture->statut === 'disponible')
                        <flux:button href="{{ route('locations.create', ['voiture_id' => $voiture->id]) }}" wire:navigate
                            variant="filled"
                            class="w-full bg-yolk-500 hover:bg-yolk-600 text-black font-black py-4 rounded-xl shadow-lg shadow-yolk-500/20 transition-all">
                            Réserver maintenant
                        </flux:button>
                    @else
                        <flux:button variant="subtle" class="w-full py-4 rounded-xl opacity-50 cursor-not-allowed" disabled>
                            Véhicule {{ $voiture->statut }}
                        </flux:button>
                    @endif
                </div>

                <!-- Admin Info if applicable -->
                @if(auth()->user()->isAdmin())
                    <div
                        class="bg-zinc-50 dark:bg-zinc-800/20 p-6 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-700">
                        <flux:heading size="sm" class="mb-4 text-zinc-500 uppercase tracking-widest">
                            {{ __('Administration') }}</flux:heading>
                        <div class="flex flex-col gap-2">
                            <flux:button href="{{ route('voitures.edit', $voiture) }}" wire:navigate icon="pencil"
                                variant="ghost" class="justify-start">Modifier la fiche</flux:button>
                            <form action="{{ route('voitures.destroy', $voiture) }}" method="POST"
                                onsubmit="return confirm('Êtes-vous sûr ?')">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" variant="ghost" icon="trash"
                                    class="justify-start text-red-500 hover:text-red-600 w-full">Supprimer le véhicule
                                </flux:button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>