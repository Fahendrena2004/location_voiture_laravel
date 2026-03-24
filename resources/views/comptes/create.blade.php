<x-layouts::app :title="__('Nouveau Compte de Paiement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button as="a" href="{{ route('comptes.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Nouveau Compte de Paiement</flux:heading>
        </div>

        <form action="{{ route('comptes.store') }}" method="POST" class="space-y-6">
            @csrf

            <flux:field>
                <flux:label>Type de compte</flux:label>
                <select name="type" required
                    class="w-full bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    <option value="bancaire" {{ old('type') === 'bancaire' ? 'selected' : '' }}>Banque</option>
                    <option value="mobile_money" {{ old('type') === 'mobile_money' ? 'selected' : '' }}>Mobile Money
                    </option>
                </select>
                @error('type') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
            </flux:field>

            <flux:input type="text" name="nom" label="Nom du service (ex: BNI, BOA, MVola)" value="{{ old('nom') }}"
                required />
            @error('nom') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

            <flux:input type="text" name="details" label="Détails (IBAN complet ou Numéro de téléphone)"
                value="{{ old('details') }}" required />
            @error('details') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

            <div class="mt-4">
                <flux:switch name="actif" label="Compte Actif" checked />
                <p class="text-xs text-neutral-500 mt-1">Si désactivé, ce compte n'apparaîtra plus sur les nouvelles
                    factures générées.</p>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <flux:button as="a" href="{{ route('comptes.index') }}" wire:navigate variant="ghost">Annuler
                </flux:button>
                <flux:button type="submit" variant="primary">Enregistrer</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>