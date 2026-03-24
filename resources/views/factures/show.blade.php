@if(request()->has('print'))
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <title>Facture {{ $facture->numero_facture }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body,
            html {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @media print {
                @page {
                    margin: 0;
                }

                body {
                    padding: 1cm;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }
        </style>
    </head>

    <body class="bg-white antialiased text-black">
        <div class="max-w-4xl mx-auto print:max-w-none">
            @if(request('template', 'classic') === 'modern')
                @include('factures.templates.modern')
            @else
                @include('factures.templates.classic')
            @endif
        </div>

        <!-- Script pour déclencher l'impression automatiquement -->
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () {
                    window.print();
                }, 800); // Léger delai pour le rendu Tailwind JIT le cas échéant
            });
        </script>
    </body>

    </html>
@else
    <x-layouts::app :title="__('Facture') . ' ' . $facture->numero_facture">
        <div class="flex w-full flex-col gap-4 rounded-xl max-w-4xl mx-auto">
            <!-- Barre d'outils (ne s'imprime pas) -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between no-print mb-4 gap-4">
                <div class="flex items-center gap-4">
                    <flux:button href="{{ route('factures.index') }}" wire:navigate icon="chevron-left" variant="ghost" />
                    <flux:heading size="xl">Facture {{ $facture->numero_facture }}</flux:heading>
                </div>

                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('factures.show', $facture) }}" class="flex items-center gap-2">
                        <label for="template" class="text-sm text-neutral-500 font-medium">Modèle :</label>
                        <select name="template" id="template" onchange="this.form.submit()"
                            class="bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 rounded-lg p-2 text-sm shadow-sm">
                            <option value="classic" {{ request('template', 'classic') === 'classic' ? 'selected' : '' }}>
                                Classique</option>
                            <option value="modern" {{ request('template') === 'modern' ? 'selected' : '' }}>Moderne</option>
                        </select>
                    </form>

                    <flux:button as="a" href="{{ route('factures.pdf', $facture) }}" icon="arrow-down-tray" variant="ghost">
                        Télécharger PDF</flux:button>
                    <!-- Bouton ouvrant la facture dans une fênetre isolée pour l'impression propre -->
                    <flux:button as="a"
                        href="{{ route('factures.show', ['facture' => $facture->id, 'template' => request('template', 'classic'), 'print' => 1]) }}"
                        target="_blank" icon="printer" variant="primary">Imprimer</flux:button>
                </div>
            </div>

            <!-- Corps de la facture (aperçu) -->
            @if(request('template', 'classic') === 'modern')
                @include('factures.templates.modern')
            @else
                @include('factures.templates.classic')
            @endif

        </div>
    </x-layouts::app>
@endif