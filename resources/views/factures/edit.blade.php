<x-layouts::app :title="__('Modifier Facture')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('factures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier la Facture</flux:heading>
        </div>

        <form action="{{ route('factures.update', $facture) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <flux:input name="numero_facture" label="Numéro de Facture" value="{{ old('numero_facture', $facture->numero_facture) }}" required />

            <flux:select name="location_id" label="Location" required>
                @foreach($locations as $location)
                    <flux:select.option value="{{ $location->id }}" {{ old('location_id', $facture->location_id) == $location->id ? 'selected' : '' }}>
                        {{ $location->client->nom }} {{ $location->client->prenom }} - {{ $location->voiture->marque }} ({{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="date" name="date_facture" label="Date de la facture" value="{{ old('date_facture', $facture->date_facture) }}" required />

            <flux:input type="number" step="0.01" name="montant_total" label="Montant Total (€)" value="{{ old('montant_total', $facture->montant_total) }}" required />

            <flux:select name="statut" label="Statut" required>
                <flux:select.option value="en attente" {{ old('statut', $facture->statut) === 'en attente' ? 'selected' : '' }}>En attente</flux:select.option>
                <flux:select.option value="payée" {{ old('statut', $facture->statut) === 'payée' ? 'selected' : '' }}>Payée</flux:select.option>
                <flux:select.option value="annulée" {{ old('statut', $facture->statut) === 'annulée' ? 'selected' : '' }}>Annulée</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('factures.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
