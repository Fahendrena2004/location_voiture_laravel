<x-layouts::app :title="__('Générer Facture')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button as="a" href="{{ route('factures.index') }}" wire:navigate icon="chevron-left"
                variant="ghost" />
            <flux:heading size="xl">Générer une Facture</flux:heading>
        </div>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-neutral-800 dark:text-red-400 border border-red-200 dark:border-red-900"
                role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('factures.store') }}" method="POST" class="space-y-6">
            @csrf

            <flux:field>
                <flux:label>Sélectionner une Location</flux:label>
                <select name="location_id" required
                    class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    <option value="" disabled selected>Choisir une location...</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ (old('location_id') == $location->id || request('location_id') == $location->id) ? 'selected' : '' }}>
                            {{ $location->client->nom }} {{ $location->client->prenom }} -
                            {{ $location->voitures->pluck('marque')->join(', ') }}
                            ({{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}) -
                            {{ number_format($location->tarif_total, 2) }} €
                        </option>
                    @endforeach
                </select>
                @error('location_id') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            @if($locations->isEmpty())
                <flux:text color="red">Toutes les locations ont déjà une facture ou il n'y a pas de locations.</flux:text>
            @endif

            <flux:input type="date" name="date_facture" label="Date de la facture"
                value="{{ old('date_facture', date('Y-m-d')) }}" required />
            @error('date_facture') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

            <flux:field>
                <flux:label>Statut de la facture</flux:label>
                <select name="statut" required
                    class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    <option value="en attente" {{ old('statut') === 'en attente' ? 'selected' : '' }}>En attente</option>
                    <option value="payée" {{ old('statut') === 'payée' ? 'selected' : '' }}>Payée</option>
                    <option value="annulée" {{ old('statut') === 'annulée' ? 'selected' : '' }}>Annulée</option>
                </select>
                @error('statut') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button as="a" href="{{ route('factures.index') }}" wire:navigate variant="ghost">Annuler
                </flux:button>
                <button type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors {{ $locations->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $locations->isEmpty() ? 'disabled' : '' }}>
                    Générer
                </button>
            </div>
        </form>
    </div>
</x-layouts::app>