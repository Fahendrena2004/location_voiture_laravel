<x-layouts::app :title="__('Modifier Utilisateur')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('users.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier l'Utilisateur</flux:heading>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <flux:field>
                <flux:label>Nom Complet</flux:label>
                <flux:input name="name" value="{{ old('name', $user->name) }}" required />
                @error('name') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                @error('email') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <flux:radio.group name="role" label="Rôle">
                <flux:radio value="client" label="Client" {{ old('role', $user->role) == 'client' ? 'checked' : '' }} />
                <flux:radio value="admin" label="Administrateur" {{ old('role', $user->role) == 'admin' ? 'checked' : '' }} />
            </flux:radio.group>

            <p class="text-sm text-neutral-500 italic mt-4">Note: Le mot de passe ne peut être modifié ici. Utilisez la
                section Paramètres de sécurité pour cela.</p>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('users.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>