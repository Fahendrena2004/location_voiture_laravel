<x-layouts::app :title="__('Nouvelle Location')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('locations.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Nouvelle Location</flux:heading>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-6" id="location-form" x-data="{ 
                statut: '{{ auth()->user()->isClient() ? 'en attente' : old('statut', 'en cours') }}',
                avec_chauffeur: {{ old('avec_chauffeur') ? 'true' : 'false' }},
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

            <div class="grid grid-cols-1 gap-6">
                @if(auth()->user()->isClient())
                    <input type="hidden" name="client_id" value="{{ $clients->first()->id }}">
                    <flux:field>
                        <flux:label>Client</flux:label>
                        <flux:input value="{{ $clients->first()->nom }} {{ $clients->first()->prenom }}" readonly />
                    </flux:field>
                @else
                    <flux:select name="client_id" label="Sélectionner le Client" placeholder="Rechercher un client..."
                        searchable required>
                        @foreach($clients as $client)
                            <flux:select.option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->nom }} {{ $client->prenom }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <flux:field>
                    <flux:label>Sélectionner les Véhicules</flux:label>
                    <select name="voitures[]" multiple required @change="updateTotal()"
                        class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm h-40 focus:ring-2 focus:ring-primary shadow-sm">
                        @foreach($voitures as $voiture)
                            <option value="{{ $voiture->id }}" data-prix="{{ $voiture->prix_journalier }}" {{ (is_array(old('voitures')) && in_array($voiture->id, old('voitures'))) ? 'selected' : '' }}>
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
                                    <option value="{{ $chauffeur->id }}" {{ (is_array(old('chauffeurs')) && in_array($chauffeur->id, old('chauffeurs'))) ? 'selected' : '' }}>
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
                    value="{{ old('date_debut', date('Y-m-d')) }}" required />
                <div>
                    <flux:input type="date" name="date_fin" @change="updateTotal()" label="Date de fin"
                        value="{{ old('date_fin', date('Y-m-d', strtotime('+1 day'))) }}" required />
                    <p class="mt-1 text-xs text-neutral-500 italic">* Un retard de retour entraîne une pénalité de 10 €
                        / jour.</p>
                </div>
            </div>
            @error('date_debut') <flux:error>{{ $message }}</flux:error> @enderror
            @error('date_fin') <flux:error>{{ $message }}</flux:error> @enderror

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input type="number" step="0.01" name="tarif_total" id="tarif_total_input"
                        value="{{ old('tarif_total') }}" label="Tarif Total (€)" required />
                    @error('tarif_total') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                @if(auth()->user()->isAdmin())
                    <flux:field>
                        <flux:label>Statut</flux:label>
                        <select name="statut" id="statut_select" x-model="statut" required
                            class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                            <option value="en attente">En attente</option>
                            <option value="en cours">En cours</option>
                            <option value="terminée">Terminée</option>
                            <option value="annulée">Annulée</option>
                        </select>
                        @error('statut') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                @else
                    <input type="hidden" name="statut" x-model="statut">
                @endif

                <div class="col-span-1 md:col-span-2" x-show="statut === 'terminée'" x-cloak>
                    <flux:input type="date" name="date_retour" id="date_retour_input" @change="updateTotal()"
                        label="Date de retour réelle" value="{{ old('date_retour') }}" />
                    @error('date_retour') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <flux:button as="a" href="{{ route('locations.index') }}" wire:navigate variant="ghost">Annuler
                </flux:button>
                <flux:button type="submit" variant="primary">Enregistrer</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>