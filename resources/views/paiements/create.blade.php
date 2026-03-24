<x-layouts::app :title="__('Nouveau Paiement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('paiements.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Nouveau Paiement</flux:heading>
        </div>

        <form action="{{ route('paiements.store') }}" method="POST" class="space-y-6">
            @csrf

            <flux:field>
                <flux:label>Location (Client & Voiture)</flux:label>
                <select name="location_id" required onchange="updateAmount()"
                    class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    <option value="" disabled selected>Choisir une location...</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" data-prix="{{ $location->tarif_total }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->client->nom }} {{ $location->client->prenom }} —
                            {{ mb_strimwidth($location->voitures->pluck('marque')->join(', '), 0, 30, '...') }}
                            ({{ \Carbon\Carbon::parse($location->date_debut)->format('d/m/Y') }}) —
                            {{ \App\Helpers\CurrencyHelper::format($location->tarif_total) }}
                        </option>
                    @endforeach
                </select>
            </flux:field>

            <flux:input type="date" name="date_paiement" label="Date du paiement"
                value="{{ old('date_paiement', date('Y-m-d')) }}" required />

            <flux:input type="number" step="0.01" name="montant" id="montant_input" label="Montant (€)"
                value="{{ old('montant') }}" required />

            <div x-data="{ mode: '{{ old('mode_paiement', 'espèces') }}' }" class="space-y-6">
                <flux:field>
                    <flux:label>Mode de paiement</flux:label>
                    <select name="mode_paiement" x-model="mode" required
                        class="w-full bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                        <option value="espèces">Espèces</option>
                        <option value="bancaire">Virement Bancaire</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </flux:field>

                <div x-show="mode === 'bancaire'" style="display: none;" class="space-y-6">
                    <flux:input type="text" name="nom_banque" label="Nom de la banque (ex: BNI, BOA...)"
                        value="{{ old('nom_banque') }}" />
                    <flux:input type="text" name="numero_bordereau" label="Numéro de bordereau"
                        value="{{ old('numero_bordereau') }}" />
                </div>

                <div x-show="mode === 'mobile_money'" style="display: none;">
                    <flux:input type="text" name="numero_mobile" label="Numéro de mobile (Expéditeur ou Cash Point)"
                        value="{{ old('numero_mobile') }}" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('paiements.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Enregistrer</flux:button>
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