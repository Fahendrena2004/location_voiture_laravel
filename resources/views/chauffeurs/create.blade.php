<x-layouts::app :title="__('Nouveau Chauffeur')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('chauffeurs.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">{{ __('Ajouter un Chauffeur') }}</flux:heading>
        </div>

        <form action="{{ route('chauffeurs.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Nom') }}</flux:label>
                    <flux:input name="nom" value="{{ old('nom') }}" required />
                    @error('nom') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Prénom') }}</flux:label>
                    <flux:input name="prenom" value="{{ old('prenom') }}" required />
                    @error('prenom') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Téléphone') }}</flux:label>
                    <flux:input name="telephone" value="{{ old('telephone') }}" required />
                    @error('telephone') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Catégorie de Permis') }}</flux:label>
                    <flux:input name="categorie_permis" placeholder="ex: B, C, D" value="{{ old('categorie_permis') }}" required />
                    @error('categorie_permis') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <flux:field>
                <flux:checkbox name="disponible" label="{{ __('Disponible immédiatement') }}" value="1" checked />
            </flux:field>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('chauffeurs.index') }}" wire:navigate variant="ghost">{{ __('Annuler') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Enregistrer') }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
