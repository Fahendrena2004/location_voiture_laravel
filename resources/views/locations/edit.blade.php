<x-layouts::app :title="__('Modifier Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Location</flux:heading>
        </div>

        <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6" id="location-form"
            x-data="{ 
                statut: '{{ old('statut', $location->statut) }}',
                avec_chauffeur: {{ old('avec_chauffeur', $location->avec_chauffeur) ? 'true' : 'false' }},
                updateTotal() {
                    const voituresSelect = document.querySelector('select[name=\'voitures[]\']');
                    const chauffeursSelect = document.querySelector('select[name=\'chauffeurs[]\']');
                    const dateDebutInput = document.querySelector('input[name=\'date_debut\']');
                    const dateFinInput = document.querySelector('input[name=\'date_fin\']');
                    const tarifTotalInput = document.getElementById('tarif_total_input');
                    
                    if (!voituresSelect || !dateDebutInput || !dateFinInput || !tarifTotalInput) return;

                    const start = new Date(dateDebutInput.value);
                    const end = new Date(dateFinInput.value);
                    
                    if (start && end && end >= start) {
                        let prixVoituresTotal = 0;
                        Array.from(voituresSelect.selectedOptions).forEach(option => {
                            if (option.dataset.prix) {
                                prixVoituresTotal += parseFloat(option.dataset.prix);
                            }
                        });

                        const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                        
                        let nbChauffeurs = (this.avec_chauffeur && chauffeursSelect) ? chauffeursSelect.selectedOptions.length : 0;
                        let total = diffDays * (prixVoituresTotal + (nbChauffeurs * 20));

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
            }"
            x-init="$watch('statut', value => { if(value === 'terminée') { let dr = document.getElementById('date_retour_input'); if(dr && !dr.value) dr.value = new Date().toISOString().split('T')[0]; } updateTotal(); }); $watch('avec_chauffeur', () => updateTotal()); setTimeout(() => updateTotal(), 500)">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <flux:field>
                    <flux:label>Client (Lecture seule)</flux:label>
                    <input type="hidden" name="client_id" value="{{ $location->client_id }}">
                    <div
                        class="flex items-center gap-3 p-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                        <flux:icon name="user" variant="mini" class="text-neutral-400" />
                        {{ $location->client->nom }} {{ $location->client->prenom }}
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Sélectionner les Véhicules</flux:label>
                    <select name="voitures[]" multiple required @change="updateTotal()"
                        class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm h-40 focus:ring-2 focus:ring-primary shadow-sm">
                        @foreach($voitures as $voiture)
                            <option value="{{ $voiture->id }}" data-prix="{{ $voiture->prix_journalier }}" {{ (is_array(old('voitures')) ? in_array($voiture->id, old('voitures')) : $location->voitures->contains('id', $voiture->id)) ? 'selected' : '' }}>
                                {{ $voiture->marque }} {{ $voiture->modele }} ({{ $voiture->immatriculation }}) —
                                {{ number_format($voiture->prix_journalier, 2) }} € / jour
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-neutral-500">Maintenez Ctrl (Win) ou Cmd (Mac) pour sélectionner
                        plusieurs véhicules.</p>
                </flux:field>

                <div class="space-y-4">
                    <flux:switch name="avec_chauffeur" x-model="avec_chauffeur" label="Louer avec chauffeur ?" />

                    <div x-show="avec_chauffeur" x-collapse>
                        <flux:field>
                            <flux:label>Assigner des Chauffeurs</flux:label>
                            <select name="chauffeurs[]" multiple @change="updateTotal()"
                                class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm h-32 focus:ring-2 focus:ring-primary shadow-sm">
                                @foreach($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->id }}" {{ (is_array(old('chauffeurs')) ? in_array($chauffeur->id, old('chauffeurs')) : $location->chauffeurs->contains('id', $chauffeur->id)) ? 'selected' : '' }}>
                                        {{ $chauffeur->nom }} {{ $chauffeur->prenom }} (Permis
                                        {{ $chauffeur->categorie_permis }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-neutral-500">Forfait de 20.00 € / jour par chauffeur assigné.
                            </p>
                        </flux:field>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input type="date" name="date_debut" @change="updateTotal()" label="Date de début"
                    value="{{ old('date_debut', $location->date_debut) }}" required />
                <div>
                    <flux:input type="date" name="date_fin" @change="updateTotal()" label="Date de fin"
                        value="{{ old('date_fin', $location->date_fin) }}" required />
                    <p class="mt-1 text-xs text-neutral-500 italic">* Un retard de retour entraîne une pénalité de 10 €
                        / jour.</p>
                </div>
            </div>
            @error('date_debut') <flux:error>{{ $message }}</flux:error> @enderror
            @error('date_fin') <flux:error>{{ $message }}</flux:error> @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input type="number" step="0.01" name="tarif_total" id="tarif_total_input"
                        value="{{ old('tarif_total', $location->tarif_total) }}" label="Tarif Total (€)" required />
                    @error('tarif_total') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                @if(auth()->user()->isAdmin())
                    <flux:field>
                        <flux:label>Statut</flux:label>
                        <select name="statut" id="statut_select" x-model="statut" required
                            class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                            <option value="en attente" {{ $location->statut === 'en attente' ? 'selected' : '' }}>En attente
                            </option>
                            <option value="en cours" {{ $location->statut === 'en cours' ? 'selected' : '' }}>En cours
                            </option>
                            <option value="terminée" {{ $location->statut === 'terminée' ? 'selected' : '' }}>Terminée
                            </option>
                            <option value="annulée" {{ $location->statut === 'annulée' ? 'selected' : '' }}>Annulée</option>
                        </select>
                        @error('statut') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                @else
                    <input type="hidden" name="statut" x-model="statut">
                    <flux:field>
                        <flux:label>Statut</flux:label>
                        <flux:badge variant="warning" inset="left">En attente (Validation par Admin)</flux:badge>
                    </flux:field>
                @endif

                <div class="col-span-1 md:col-span-2" x-show="statut === 'terminée'" x-cloak>
                    <flux:input type="date" name="date_retour" id="date_retour_input" @change="updateTotal()"
                        label="Date de retour réelle" value="{{ old('date_retour', $location->date_retour) }}" />
                    @error('date_retour') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <flux:button as="a" href="{{ route('locations.index') }}" wire:navigate variant="ghost">Annuler
                </flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>