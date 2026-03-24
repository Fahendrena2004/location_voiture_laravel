<x-layouts::app :title="__('Modifier Entretien')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('entretiens.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Entretien</flux:heading>
        </div>

        <form action="{{ route('entretiens.update', $entretien) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <flux:field>
                <flux:label>Voiture</flux:label>
                <select name="voiture_id" required
                    class="w-full bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm">
                    <option value="" disabled>Choisir une voiture...</option>
                    @foreach($voitures as $voiture)
                        <option value="{{ $voiture->id }}" {{ old('voiture_id', $entretien->voiture_id) == $voiture->id ? 'selected' : '' }}>
                            {{ $voiture->marque }} {{ $voiture->modele }} ({{ $voiture->immatriculation }})
                        </option>
                    @endforeach
                </select>
            </flux:field>

            <flux:input type="date" name="date_entretien" label="Date de l'entretien"
                value="{{ old('date_entretien', $entretien->date_entretien) }}" required />

            <flux:textarea name="description" label="Description des travaux" required>
                {{ old('description', $entretien->description) }}</flux:textarea>

            <flux:input type="number" step="0.01" name="cout" label="Coût de l'entretien (€)"
                value="{{ old('cout', $entretien->cout) }}" required />

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('entretiens.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>