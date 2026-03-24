<x-layouts::app :title="__('Nouvel Utilisateur')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('users.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Nouvel Utilisateur</flux:heading>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <flux:field>
                <flux:label>Nom Complet</flux:label>
                <flux:input name="name" value="{{ old('name') }}" required />
                @error('name') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" name="email" value="{{ old('email') }}" required />
                @error('email') <flux:error>{{ $message }}</flux:error> @enderror
            </flux:field>

            <flux:radio.group name="role" label="Rôle">
                <flux:radio value="client" label="Client" {{ old('role', 'client') == 'client' ? 'checked' : '' }} />
                <flux:radio value="admin" label="Administrateur" {{ old('role') == 'admin' ? 'checked' : '' }} />
            </flux:radio.group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Mot de passe</flux:label>
                    <flux:input type="password" name="password" required />
                    @error('password') <flux:error>{{ $message }}</flux:error> @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Confirmer le mot de passe</flux:label>
                    <flux:input type="password" name="password_confirmation" required />
                </flux:field>
            </div>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('users.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Enregistrer</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>