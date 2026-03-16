<x-layouts::app :title="__('Modifier Paiement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('paiements.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Paiement</flux:heading>
        </div>

        <form action="{{ route('paiements.update', $paiement) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <flux:select name="location_id" label="Location" required>
                @foreach($locations as $location)
                    <flux:select.option value="{{ $location->id }}" {{ old('location_id', $paiement->location_id) == $location->id ? 'selected' : '' }}>
                        {{ $location->client->nom }} {{ $location->client->prenom }} - {{ $location->voiture->marque }} ({{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }})
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input type="date" name="date_paiement" label="Date du paiement" value="{{ old('date_paiement', $paiement->date_paiement) }}" required />

            <flux:input type="number" step="0.01" name="montant" label="Montant (€)" value="{{ old('montant', $paiement->montant) }}" required />

            <flux:select name="mode_paiement" label="Mode de paiement" required>
                <flux:select.option value="espèces" {{ old('mode_paiement', $paiement->mode_paiement) === 'espèces' ? 'selected' : '' }}>Espèces</flux:select.option>
                <flux:select.option value="carte" {{ old('mode_paiement', $paiement->mode_paiement) === 'carte' ? 'selected' : '' }}>Carte</flux:select.option>
                <flux:select.option value="virement" {{ old('mode_paiement', $paiement->mode_paiement) === 'virement' ? 'selected' : '' }}>Virement</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('paiements.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
