<x-layouts::app :title="__('Modifier Paiement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('paiements.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Paiement</flux:heading>
        </div>

        <form action="{{ route('paiements.update', $paiement) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <flux:field>
                <flux:label>Location</flux:label>
                <select name="location_id" required onchange="updateAmount()"
                    class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" data-prix="{{ $location->tarif_total }}" {{ old('location_id', $paiement->location_id) == $location->id ? 'selected' : '' }}>
                            @if(auth()->user()->isAdmin())
                                {{ $location->client->nom }} {{ $location->client->prenom }} -
                            @endif
                            {{ Str::limit($location->voitures->pluck('marque')->join(', '), 30) }}
                            ({{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}) -
                            {{ number_format($location->tarif_total, 2) }}€
                        </option>
                    @endforeach
                </select>
            </flux:field>

            <flux:input type="date" name="date_paiement" label="Date du paiement"
                value="{{ old('date_paiement', $paiement->date_paiement) }}" required />

            <flux:input type="number" step="0.01" name="montant" id="montant_input" label="Montant (€)"
                value="{{ old('montant', $paiement->montant) }}" required />

            <div x-data="{ mode: '{{ old('mode_paiement', $paiement->mode_paiement) }}' }" class="space-y-6">
                <flux:select name="mode_paiement" label="Mode de paiement" required x-model="mode">
                    <flux:select.option value="espèces">Espèces</flux:select.option>
                    <flux:select.option value="bancaire">Virement Bancaire</flux:select.option>
                    <flux:select.option value="mobile_money">Mobile Money</flux:select.option>
                </flux:select>

                <div x-show="mode === 'bancaire'" style="display: none;" class="space-y-6">
                    <flux:input type="text" name="nom_banque" label="Nom de la banque (ex: BNI, BOA...)"
                        value="{{ old('nom_banque', $paiement->nom_banque) }}" />
                    <flux:input type="text" name="numero_bordereau" label="Numéro de bordereau"
                        value="{{ old('numero_bordereau', $paiement->numero_bordereau) }}" />
                </div>

                <div x-show="mode === 'mobile_money'" style="display: none;">
                    <flux:input type="text" name="numero_mobile" label="Numéro de mobile (Expéditeur ou Cash Point)"
                        value="{{ old('numero_mobile', $paiement->numero_mobile) }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('paiements.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>

    <script>
        function updateAmount() {
            const locationSelect = document.querySelector('select[name="location_id"]');
            const montantInput = document.getElementById('montant_input');

            if (locationSelect && montantInput) {
                const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.prix) {
                    montantInput.value = selectedOption.dataset.prix;
                }
            }
        }
    </script>
</x-layouts::app>