<x-layouts::app :title="__('Modifier Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Location</flux:heading>
        </div>

        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-4" id="location-form"
            x-data="{ 
                avecChauffeur: {{ old('avec_chauffeur', $location->avec_chauffeur) ? 'true' : 'false' }}, 
                statut: '{{ old('statut', $location->statut) }}',
                updateTotal() {
                    const voitureSelect = document.querySelector('select[name=\'voiture_id\']');
                    const dateDebutInput = document.querySelector('input[name=\'date_debut\']');
                    const dateFinInput = document.querySelector('input[name=\'date_fin\']');
                    const tarifTotalInput = document.getElementById('tarif_total_input');
                    
                    if (!voitureSelect || !dateDebutInput || !dateFinInput || !tarifTotalInput) return;

                    const start = new Date(dateDebutInput.value);
                    const end = new Date(dateFinInput.value);
                    const selectedOption = voitureSelect.options[voitureSelect.selectedIndex];
                    
                    if (start && end && end >= start && selectedOption && selectedOption.dataset.prix) {
                        const prixVoiture = parseFloat(selectedOption.dataset.prix);
                        const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                        
                        let total = diffDays * (prixVoiture + (this.avecChauffeur ? 20 : 0));

                        const dateRetourInput = document.getElementById('date_retour_input');
                        if (this.statut === 'terminée' && dateRetourInput && dateRetourInput.value) {
                            const realEnd = new Date(dateRetourInput.value);
                            if (realEnd > end) {
                                const lateDays = Math.ceil(Math.abs(realEnd - end) / (1000 * 60 * 60 * 24));
                                total += (lateDays * 10);
                            }
                        }

                        tarifTotalInput.value = total.toFixed(2);
                    }
                }
            }" x-init="$watch('statut', value => { if(value === 'terminée') { let dr = document.getElementById('date_retour_input'); if(dr && !dr.value) dr.value = new Date().toISOString().split('T')[0]; } updateTotal(); }); setTimeout(() => updateTotal(), 500)">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Client</flux:label>
                    <input type="hidden" name="client_id" value="{{ $location->client_id }}">
                    <div class="p-2 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 text-sm">
                        {{ $location->client->nom }} {{ $location->client->prenom }}
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Voiture</flux:label>
                    <select name="voiture_id" required @change="updateTotal()" class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                        @foreach($voitures as $voiture)
                            <option value="{{ $voiture->id }}" data-prix="{{ $voiture->prix_journalier }}" {{ old('voiture_id', $location->voiture_id) == $voiture->id ? 'selected' : '' }}>
                                {{ $voiture->marque }} {{ $voiture->modele }} ({{ $voiture->immatriculation }}) - {{ number_format($voiture->prix_journalier, 2) }}€/j
                            </option>
                        @endforeach
                    </select>
                    @error('voiture_id') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <flux:field>
                    <input type="hidden" name="avec_chauffeur" value="0">
                    <flux:switch name="avec_chauffeur" id="avec_chauffeur_switch" label="Louer avec chauffeur ?" value="1" x-model="avecChauffeur" @change="updateTotal()" />
                </flux:field>

                <div id="chauffeur-selection" x-show="avecChauffeur" x-cloak>
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
                <flux:input type="date" name="date_debut" @change="updateTotal()" label="Date de début" value="{{ old('date_debut', $location->date_debut) }}" required />
                <div>
                    <flux:input type="date" name="date_fin" @change="updateTotal()" label="Date de fin" value="{{ old('date_fin', $location->date_fin) }}" required />
                    <p class="mt-1 text-xs text-neutral-500 italic">* Un retard de retour entraîne une pénalité de 10 € / jour.</p>
                </div>
            </div>
            @error('date_debut') <flux:error>{{ $message }}</flux:error> @enderror
            @error('date_fin') <flux:error>{{ $message }}</flux:error> @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input type="number" step="0.01" name="tarif_total" id="tarif_total_input" value="{{ old('tarif_total', $location->tarif_total) }}" label="Tarif Total (€)" required />
                    @error('tarif_total') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
                
                <flux:field>
                    <flux:label>Statut</flux:label>
                    <select name="statut" id="statut_select" x-model="statut" required class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                        <option value="en cours">En cours</option>
                        <option value="terminée">Terminée</option>
                        <option value="annulée">Annulée</option>
                    </select>
                    @error('statut') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <div class="col-span-1 md:col-span-2" x-show="statut === 'terminée'" x-cloak>
                    <flux:input type="date" name="date_retour" id="date_retour_input" @change="updateTotal()" label="Date de retour réelle" value="{{ old('date_retour', $location->date_retour) }}" />
                    @error('date_retour') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <flux:button as="a" href="{{ route('locations.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
