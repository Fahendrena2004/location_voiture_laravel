<x-layouts::app :title="__('Entretiens')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Entretiens</flux:heading>
            <flux:button href="{{ route('entretiens.create') }}" wire:navigate icon="plus" variant="primary">Nouvel Entretien</flux:button>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-neutral-800 dark:text-green-400 border border-green-200 dark:border-green-900" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 px-4 sm:px-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Voiture</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Description</flux:table.column>
                    <flux:table.column>Coût</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($entretiens as $entretien)
                        <flux:table.row>
                            <flux:table.cell>{{ $entretien->voiture->marque }} {{ $entretien->voiture->modele }} ({{ $entretien->voiture->immatriculation }})</flux:table.cell>
                            <flux:table.cell>{{ \Carbon\Carbon::parse($entretien->date_entretien)->format('d/m/Y') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="max-w-xs truncate">{{ $entretien->description }}</div>
                            </flux:table.cell>
                            <flux:table.cell>{{ number_format($entretien->cout, 2) }} €</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('entretiens.show', $entretien) }}" wire:navigate icon="eye" size="sm" variant="ghost" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300" />
                                    <flux:button href="{{ route('entretiens.edit', $entretien) }}" wire:navigate icon="pencil" size="sm" variant="ghost" class="text-amber-500 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300" />
                                    <form action="{{ route('entretiens.destroy', $entretien) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" icon="trash" size="sm" variant="ghost" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" />
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</x-layouts::app>
