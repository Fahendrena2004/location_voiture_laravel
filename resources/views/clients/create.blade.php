<x-layouts::app :title="__('Nouveau Client')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('clients.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Nouveau Client</flux:heading>
        </div>

        <form action="{{ route('clients.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input name="nom" label="Nom" value="{{ old('nom') }}" required />
                <flux:input name="prenom" label="Prénom" value="{{ old('prenom') }}" required />
            </div>

            <flux:input type="date" name="date_naissance" label="Date de naissance" value="{{ old('date_naissance') }}" required />

            <flux:input name="telephone" label="Téléphone" value="{{ old('telephone') }}" required />

            <flux:textarea name="adresse" label="Adresse" required>{{ old('adresse') }}</flux:textarea>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('clients.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Enregistrer</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
