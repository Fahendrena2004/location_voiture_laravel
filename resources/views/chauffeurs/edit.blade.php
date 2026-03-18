<x-layouts::app :title="__('Modifier Chauffeur')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('chauffeurs.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">{{ __('Modifier le Chauffeur') }} : {{ $chauffeur->nom }} {{ $chauffeur->prenom }}</flux:heading>
        </div>

        <form action="{{ route('chauffeurs.update', $chauffeur) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Nom') }}</flux:label>
                    <flux:input name="nom" value="{{ old('nom', $chauffeur->nom) }}" required />
                    @error('nom') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Prénom') }}</flux:label>
                    <flux:input name="prenom" value="{{ old('prenom', $chauffeur->prenom) }}" required />
                    @error('prenom') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Téléphone') }}</flux:label>
                    <flux:input name="telephone" value="{{ old('telephone', $chauffeur->telephone) }}" required />
                    @error('telephone') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Catégorie de Permis') }}</flux:label>
                    <flux:input name="categorie_permis" value="{{ old('categorie_permis', $chauffeur->categorie_permis) }}" required />
                    @error('categorie_permis') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>
            </div>

            <flux:field>
                <input type="hidden" name="disponible" value="0">
                <flux:checkbox name="disponible" label="{{ __('Disponible') }}" value="1" :checked="old('disponible', $chauffeur->disponible)" />
            </flux:field>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('chauffeurs.index') }}" wire:navigate variant="ghost">{{ __('Annuler') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Mettre à jour') }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
