<x-layouts::app :title="__('Modifier Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Location</flux:heading>
        </div>

        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6" id="location-form">
            @csrf
            @method('PUT')
            
            <flux:field>
                <flux:label>Client</flux:label>
                <select name="client_id" required class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $location->client_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }} {{ $client->prenom }}
                        </option>
                    @endforeach
                </select>
                @error('client_id') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Voiture</flux:label>
                <select name="voiture_id" required onchange="updateTotal()" class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    @foreach($voitures as $voiture)
                        <option value="{{ $voiture->id }}" data-prix="{{ $voiture->prix_journalier }}" {{ old('voiture_id', $location->voiture_id) == $voiture->id ? 'selected' : '' }}>
                            {{ $voiture->marque }} {{ $voiture->modele }} ({{ $voiture->immatriculation }}) - {{ number_format($voiture->prix_journalier, 2) }}€/j
                        </option>
                    @endforeach
                </select>
                @error('voiture_id') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <div class="space-y-4">
                <flux:field>
                    <input type="hidden" name="avec_chauffeur" value="0">
                    <flux:switch name="avec_chauffeur" id="avec_chauffeur_switch" label="Louer avec chauffeur ?" value="1" onchange="toggleChauffeur()" {{ old('avec_chauffeur', $location->avec_chauffeur) ? 'checked' : '' }} />
                </flux:field>

                <div id="chauffeur-selection" style="display: {{ old('avec_chauffeur', $location->avec_chauffeur) ? 'block' : 'none' }}">
                    <flux:field>
                        <flux:label>Chauffeur</flux:label>
                        <select name="chauffeur_id" class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                            <option value="" disabled selected>Choisir un chauffeur...</option>
                            @foreach($chauffeurs as $chauffeur)
                                <option value="{{ $chauffeur->id }}" {{ old('chauffeur_id', $location->chauffeur_id) == $chauffeur->id ? 'selected' : '' }}>
                                    {{ $chauffeur->nom }} {{ $chauffeur->prenom }} ({{ $chauffeur->categorie_permis }})
                                </option>
                            @endforeach
                        </select>
                        @error('chauffeur_id') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input type="date" name="date_debut" onchange="updateTotal()" label="Date de début" value="{{ old('date_debut', $location->date_debut) }}" required />
                <flux:input type="date" name="date_fin" onchange="updateTotal()" label="Date de fin" value="{{ old('date_fin', $location->date_fin) }}" required />
            </div>
            @error('date_debut') <flux:error>{{ $message }}</flux:error> @enderror
            @error('date_fin') <flux:error>{{ $message }}</flux:error> @enderror

            <flux:input type="number" step="0.01" name="tarif_total" id="tarif_total_input" value="{{ old('tarif_total', $location->tarif_total) }}" label="Tarif Total (€)" required />
            @error('tarif_total') <flux:error>{{ $message }}</flux:error> @enderror

            <flux:field>
                <flux:label>Statut</flux:label>
                <select name="statut" required class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    <option value="en cours" {{ old('statut', $location->statut) === 'en cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminée" {{ old('statut', $location->statut) === 'terminée' ? 'selected' : '' }}>Terminée</option>
                    <option value="annulée" {{ old('statut', $location->statut) === 'annulée' ? 'selected' : '' }}>Annulée</option>
                </select>
                @error('statut') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('locations.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>

    <script>
        function toggleChauffeur() {
            const sw = document.querySelector('input[name="avec_chauffeur"][type="checkbox"]');
            const div = document.getElementById('chauffeur-selection');
            if (sw && div) {
                div.style.display = sw.checked ? 'block' : 'none';
            }
            updateTotal();
        }

        function updateTotal() {
            const voitureSelect = document.querySelector('select[name="voiture_id"]');
            const dateDebutInput = document.querySelector('input[name="date_debut"]');
            const dateFinInput = document.querySelector('input[name="date_fin"]');
            const tarifTotalInput = document.getElementById('tarif_total_input');
            const sw = document.querySelector('input[name="avec_chauffeur"][type="checkbox"]');
            
            if (!voitureSelect || !dateDebutInput || !dateFinInput || !tarifTotalInput) return;

            const start = new Date(dateDebutInput.value);
            const end = new Date(dateFinInput.value);
            const selectedOption = voitureSelect.options[voitureSelect.selectedIndex];
            
            if (start && end && end >= start && selectedOption && selectedOption.dataset.prix) {
                const prixVoiture = parseFloat(selectedOption.dataset.prix);
                const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                
                const avecChauffeur = sw ? sw.checked : false;

                let total = diffDays * (prixVoiture + (avecChauffeur ? 20 : 0));
                tarifTotalInput.value = total.toFixed(2);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(updateTotal, 500);
            const sw = document.querySelector('input[name="avec_chauffeur"][type="checkbox"]');
            const div = document.getElementById('chauffeur-selection');
            if (sw && div) {
                div.style.display = sw.checked ? 'block' : 'none';
            }
        });
    </script>
</x-layouts::app>
