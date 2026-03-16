<x-layouts::app :title="__('Générer Facture')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('factures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Générer une Facture</flux:heading>
        </div>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-neutral-800 dark:text-red-400 border border-red-200 dark:border-red-900" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('factures.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <flux:select name="location_id" label="Sélectionner une Location" placeholder="Choisir une location..." required>
                @foreach($locations as $location)
                    <flux:select.option value="{{ $location->id }}" {{ (old('location_id') == $location->id || request('location_id') == $location->id) ? 'selected' : '' }}>
                        {{ $location->client->nom }} {{ $location->client->prenom }} - {{ $location->voiture->marque }} ({{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}) - {{ number_format($location->tarif_total, 2) }} €
                    </flux:select.option>
                @endforeach
            </flux:select>
            @error('location_id') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

            @if($locations->isEmpty())
                <flux:text color="danger">Toutes les locations ont déjà une facture ou il n'y a pas de locations.</flux:text>
            @endif

            <flux:input type="date" name="date_facture" label="Date de la facture" value="{{ old('date_facture', date('Y-m-d')) }}" required />
            @error('date_facture') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

            <flux:select name="statut" label="Statut de la facture" required>
                <flux:select.option value="en attente" {{ old('statut') === 'en attente' ? 'selected' : '' }}>En attente</flux:select.option>
                <flux:select.option value="payée" {{ old('statut') === 'payée' ? 'selected' : '' }}>Payée</flux:select.option>
                <flux:select.option value="annulée" {{ old('statut') === 'annulée' ? 'selected' : '' }}>Annulée</flux:select.option>
            </flux:select>
            @error('statut') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('factures.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary" {{ $locations->isEmpty() ? 'disabled' : '' }}>Générer</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
