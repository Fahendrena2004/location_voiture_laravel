<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->isClient() && !auth()->user()->client) {
            \App\Models\Client::create([
                'user_id' => auth()->id(),
                'type' => 'personne',
                'nom' => auth()->user()->name,
                'prenom' => '',
                'telephone' => '',
                'adresse' => '',
            ]);
        }

        $query = Facture::with('location.client', 'location.voitures', 'location.chauffeurs');

        if (auth()->user()->isClient()) {
            $query->whereHas('location.client', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $factures = $query->get();
        return view('factures.index', compact('factures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $selectedLocationId = $request->query('location_id');
        // Get locations that don't have a facture yet, OR the one explicitly requested
        $locations = Location::whereDoesntHave('facture')
            ->orWhere('id', $selectedLocationId)
            ->with('client', 'voitures', 'chauffeurs')
            ->get();

        return view('factures.create', compact('locations', 'selectedLocationId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id|unique:factures,location_id',
            'date_facture' => 'required|date',
            'statut' => 'required|in:payée,en attente,annulée',
        ]);

        try {
            $location = Location::find($validated['location_id']);

            // Generate unique invoice number (e.g., FACT-2026-001)
            $year = date('Y', strtotime($validated['date_facture']));
            $lastFacture = Facture::whereYear('date_facture', $year)
                ->orderBy('id', 'desc')
                ->first();

            $nextSeq = 1;
            if ($lastFacture) {
                // Extract sequence number from FACT-YYYY-NNN
                $parts = explode('-', $lastFacture->numero_facture);
                $lastSeq = (int) end($parts);
                $nextSeq = $lastSeq + 1;
            }

            $numero_facture = 'FACT-' . $year . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

            $facture = Facture::create([
                'location_id' => $validated['location_id'],
                'numero_facture' => $numero_facture,
                'date_facture' => $validated['date_facture'],
                'montant_total' => $location->tarif_total,
                'statut' => $validated['statut'],
            ]);

            return redirect()->route('factures.show', $facture)->with('success', 'Facture générée avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la génération : ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Facture $facture)
    {
        if (auth()->user()->isClient() && $facture->location->client->user_id !== auth()->id()) {
            abort(403);
        }
        $facture->load('location.client', 'location.voitures', 'location.chauffeurs', 'location.paiements');
        $comptesPaiement = \App\Models\ComptePaiement::where('actif', true)->get();
        return view('factures.show', compact('facture', 'comptesPaiement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facture $facture)
    {
        $locations = Location::with('client', 'voitures')->get();
        return view('factures.edit', compact('facture', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facture $facture)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id|unique:factures,location_id,' . $facture->id,
            'date_facture' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'statut' => 'required|in:payée,en attente,annulée',
            'numero_facture' => 'required|string|unique:factures,numero_facture,' . $facture->id,
        ]);

        $facture->update($validated);

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facture $facture)
    {
        $facture->delete();

        return redirect()->route('factures.index')->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Download the specified resource as PDF.
     */
    public function downloadPdf(Facture $facture)
    {
        if (auth()->user()->isClient() && $facture->location->client->user_id !== auth()->id()) {
            abort(403);
        }

        $facture->load('location.client', 'location.voitures', 'location.chauffeurs', 'location.paiements');
        $comptesPaiement = \App\Models\ComptePaiement::where('actif', true)->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('factures.pdf', compact('facture', 'comptesPaiement'));

        return $pdf->download($facture->numero_facture . '.pdf');
    }
}
