<x-layouts::app :title="__('Modifier Voiture')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('voitures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Voiture: {{ $voiture->immatriculation }}</flux:heading>
        </div>

        <form action="{{ route('voitures.update', $voiture) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input name="marque" label="Marque" :value="old('marque', $voiture->marque)" required />
                <flux:input name="modele" label="Modèle" :value="old('modele', $voiture->modele)" required />
            </div>

            <flux:input name="immatriculation" label="Immatriculation"
                :value="old('immatriculation', $voiture->immatriculation)" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input name="couleur" label="Couleur" :value="old('couleur', $voiture->couleur)" />
                <flux:input type="number" name="nombre_places" label="Nombre de places"
                    :value="old('nombre_places', $voiture->nombre_places)" required />
            </div>

            <flux:input type="number" step="0.01" name="prix_journalier" label="Prix journalier (€)"
                :value="old('prix_journalier', $voiture->prix_journalier)" required />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Catégorie</flux:label>
                    <select name="categorie" required
                        class="w-full bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                        @foreach(['Berline', 'SUV', 'Citadine', 'Luxe', 'Utilitaire'] as $cat)
                            <option value="{{ $cat }}" {{ old('categorie', $voiture->categorie) === $cat ? 'selected' : '' }}>
                                {{ $cat }}</option>
                        @endforeach
                    </select>
                </flux:field>

                <flux:field>
                    <flux:label>Statut</flux:label>
                    <select name="statut" required
                        class="w-full bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                        <option value="disponible" {{ old('statut', $voiture->statut) === 'disponible' ? 'selected' : '' }}>
                            Disponible</option>
                        <option value="louée" {{ old('statut', $voiture->statut) === 'louée' ? 'selected' : '' }}>Louée
                        </option>
                        <option value="en entretien" {{ old('statut', $voiture->statut) === 'en entretien' ? 'selected' : '' }}>En entretien</option>
                    </select>
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('voitures.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>