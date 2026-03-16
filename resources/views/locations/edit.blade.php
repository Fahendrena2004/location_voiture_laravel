<x-layouts::app :title="__('Modifier Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Location</flux:heading>
        </div>

        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <flux:select name="client_id" label="Client" required>
                @foreach($clients as $client)
                    <flux:select.option value="{{ $client->id }}" {{ old('client_id', $location->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->nom }} {{ $client->prenom }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select name="voiture_id" label="Voiture" required>
                @foreach($voitures as $voiture)
                    <flux:select.option value="{{ $voiture->id }}" data-prix="{{ $voiture->prix_journalier }}" {{ old('voiture_id', $location->voiture_id) == $voiture->id ? 'selected' : '' }}>
                        {{ $voiture->marque }} {{ $voiture->modele }} ({{ $voiture->immatriculation }}) - {{ number_format($voiture->prix_journalier, 2) }}€/j
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input type="date" name="date_debut" label="Date de début" value="{{ old('date_debut', $location->date_debut) }}" required />
                <flux:input type="date" name="date_fin" label="Date de fin" value="{{ old('date_fin', $location->date_fin) }}" required />
            </div>

            <flux:input type="number" step="0.01" name="tarif_total" label="Tarif Total (€)" value="{{ old('tarif_total', $location->tarif_total) }}" required />

            <flux:select name="statut" label="Statut" required>
                <flux:select.option value="en cours" {{ old('statut', $location->statut) === 'en cours' ? 'selected' : '' }}>En cours</flux:select.option>
                <flux:select.option value="terminée" {{ old('statut', $location->statut) === 'terminée' ? 'selected' : '' }}>Terminée</flux:select.option>
                <flux:select.option value="annulée" {{ old('statut', $location->statut) === 'annulée' ? 'selected' : '' }}>Annulée</flux:select.option>
            </flux:select>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('locations.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const voitureSelect = document.querySelector('select[name="voiture_id"]');
            const dateDebutInput = document.querySelector('input[name="date_debut"]');
            const dateFinInput = document.querySelector('input[name="date_fin"]');
            const tarifTotalInput = document.querySelector('input[name="tarif_total"]');

            function calculateTotal() {
                const voitureId = voitureSelect.value;
                const dateDebut = new Date(dateDebutInput.value);
                const dateFin = new Date(dateFinInput.value);

                if (voitureId && dateDebut && dateFin && dateFin >= dateDebut) {
                    const selectedOption = voitureSelect.options[voitureSelect.selectedIndex];
                    const prixJournalier = parseFloat(selectedOption.getAttribute('data-prix'));
                    
                    const diffTime = Math.abs(dateFin - dateDebut);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both days
                    
                    tarifTotalInput.value = (diffDays * prixJournalier).toFixed(2);
                }
            }

            voitureSelect.addEventListener('change', calculateTotal);
            dateDebutInput.addEventListener('change', calculateTotal);
            dateFinInput.addEventListener('change', calculateTotal);
        });
    </script>
</x-layouts::app>
