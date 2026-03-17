<x-layouts::app :title="__('Modifier Client')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl max-w-2xl mx-auto">
        <div class="flex items-center gap-4">
            <flux:button href="{{ route('clients.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
            <flux:heading size="xl">Modifier Client: {{ $client->nom }} {{ $client->prenom }}</flux:heading>
        </div>

        <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6" x-data="{ type: '{{ old('type', $client->type) }}' }">
            @csrf
            @method('PUT')
            
            <flux:radio.group name="type" label="Type de Client" x-model="type">
                <flux:radio value="personne" label="Personne Physique" />
                <flux:radio value="association" label="Association / Société" />
            </flux:radio.group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label x-text="type === 'personne' ? 'Nom' : 'Raison Sociale'"></flux:label>
                    <flux:input name="nom" :value="old('nom', $client->nom)" required />
                </flux:field>
                <template x-if="type === 'personne'">
                    <flux:input name="prenom" label="Prénom" :value="old('prenom', $client->prenom)" />
                </template>
                <template x-if="type === 'association'">
                    <flux:input name="nif" label="NIF" :value="old('nif', $client->nif)" />
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-if="type === 'personne'">
                    <flux:input type="date" name="date_naissance" label="Date de naissance" :value="old('date_naissance', $client->date_naissance)" />
                </template>
                <template x-if="type === 'personne'">
                    <flux:input name="cin" label="CIN" :value="old('cin', $client->cin)" />
                </template>
                <template x-if="type === 'association'">
                    <flux:input name="stat" label="STAT" :value="old('stat', $client->stat)" />
                </template>
            </div>

            <flux:input name="telephone" label="Téléphone" :value="old('telephone', $client->telephone)" required />

            <flux:textarea name="adresse" label="Adresse" required>{{ old('adresse', $client->adresse) }}</flux:textarea>

            <div class="flex justify-end gap-2 mt-8">
                <flux:button href="{{ route('clients.index') }}" wire:navigate variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
