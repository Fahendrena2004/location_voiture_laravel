<x-layouts::app :title="__('Gestion des Utilisateurs')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Utilisateurs</flux:heading>
            <flux:button href="{{ route('users.create') }}" wire:navigate icon="plus" variant="primary">Nouvel
                Utilisateur</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900"
                role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-neutral-800 dark:text-red-400 border border-red-200 dark:border-red-900"
                role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nom</flux:table.column>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Rôle</flux:table.column>
                    <flux:table.column>Profil Client</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($users as $user)
                        <flux:table.row>
                            <flux:table.cell class="font-medium">{{ $user->name }}</flux:table.cell>
                            <flux:table.cell>{{ $user->email }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="{{ $user->isAdmin() ? 'primary' : 'neutral' }}" inset="left">
                                    {{ ucfirst($user->role) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($user->client)
                                    <flux:button href="{{ route('clients.show', $user->client) }}" wire:navigate size="sm"
                                        variant="ghost" icon="user">Voir Profil</flux:button>
                                @elseif($user->isClient())
                                    <flux:button href="{{ route('clients.create', ['user_id' => $user->id]) }}" wire:navigate
                                        size="sm" variant="ghost" icon="plus" class="text-amber-600">Lier Profil</flux:button>
                                @else
                                    <span class="text-xs text-neutral-400 italic">N/A (Admin)</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown>
                                    <flux:button variant="outline" size="sm" icon="ellipsis-vertical"
                                        class="rounded-full" />

                                    <flux:menu>
                                        <flux:menu.item href="{{ route('users.edit', $user) }}" wire:navigate icon="pencil">
                                            Modifier</flux:menu.item>

                                        @if($user->id !== auth()->id())
                                            <flux:menu.separator />

                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:menu.item type="submit" as="button" icon="trash" variant="danger">
                                                    Supprimer</flux:menu.item>
                                            </form>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>