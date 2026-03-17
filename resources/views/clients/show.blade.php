<x-layouts::app :title="__('Détails Client')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <flux:button href="{{ route('clients.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                <flux:heading size="xl">Détails du Client</flux:heading>
            </div>
            <flux:button href="{{ route('clients.edit', $client) }}" wire:navigate icon="pencil" variant="primary">Modifier</flux:button>
        </div>

        <div class="bg-white dark:bg-neutral-800 p-6 rounded-xl border border-neutral-200 dark:border-neutral-700 space-y-4">
            <div class="flex items-center justify-between border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <flux:label class="text-xs uppercase text-neutral-500">Type de Client</flux:label>
                <flux:badge :icon="$client->type === 'personne' ? 'user' : 'building-office'" variant="filled" :class="$client->type === 'personne' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'">
                    {{ $client->type === 'personne' ? 'Personne Physique' : 'Association / Société' }}
                </flux:badge>
            </div>

            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">{{ $client->type === 'personne' ? 'Nom' : 'Raison Sociale' }}</flux:label>
                    <div class="font-medium text-lg">{{ $client->nom }}</div>
                </div>
                @if($client->type === 'personne')
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Prénom</flux:label>
                    <div class="font-medium text-lg">{{ $client->prenom }}</div>
                </div>
                @endif
            </div>

            @if($client->type === 'personne')
            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">Date de naissance</flux:label>
                    <div class="font-medium">{{ \Carbon\Carbon::parse($client->date_naissance)->format('d/m/Y') }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">CIN</flux:label>
                    <div class="font-medium">{{ $client->cin ?? 'Non renseigné' }}</div>
                </div>
            </div>
            @else
            <div class="grid grid-cols-2 gap-4 border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">NIF</flux:label>
                    <div class="font-medium">{{ $client->nif ?? 'Non renseigné' }}</div>
                </div>
                <div>
                    <flux:label class="text-xs uppercase text-neutral-500">STAT</flux:label>
                    <div class="font-medium">{{ $client->stat ?? 'Non renseigné' }}</div>
                </div>
            </div>
            @endif

            <div class="border-b pb-4 border-neutral-100 dark:border-neutral-700">
                <flux:label class="text-xs uppercase text-neutral-500">Téléphone</flux:label>
                <div class="font-medium">{{ $client->telephone }}</div>
            </div>

            <div>
                <flux:label class="text-xs uppercase text-neutral-500">Adresse</flux:label>
                <div class="font-medium">{{ $client->adresse }}</div>
            </div>
        </div>
    </div>
</x-layouts::app>
