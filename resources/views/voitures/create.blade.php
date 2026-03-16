<x-layouts::app :title="__('Nouvelle Voiture')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('voitures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Nouvelle Voiture</flux:heading>
        </div>

        <form action="{{ route('voitures.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input name="marque" label="Marque" value="{{ old('marque') }}" required />
                <flux:input name="modele" label="Modèle" value="{{ old('modele') }}" required />
            </div>

            <flux:input name="immatriculation" label="Immatriculation" value="{{ old('immatriculation') }}" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input name="couleur" label="Couleur" value="{{ old('couleur') }}" />
                <flux:input type="number" step="0.01" name="prix_journalier" label="Prix journalier (€)" value="{{ old('prix_journalier') }}" required />
            </div>

            <flux:select name="statut" label="Statut" required>
                <flux:select.option value="disponible" {{ old('statut') === 'disponible' ? 'selected' : '' }}>Disponible</flux:select.option>
                <flux:select.option value="louée" {{ old('statut') === 'louée' ? 'selected' : '' }}>Louée</flux:select.option>
                <flux:select.option value="en entretien" {{ old('statut') === 'en entretien' ? 'selected' : '' }}>En entretien</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('voitures.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Enregistrer</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
