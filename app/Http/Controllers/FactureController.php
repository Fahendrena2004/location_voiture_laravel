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
        $factures = Facture::with('location.client', 'location.voiture')->get();
        return view('factures.index', compact('factures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get locations that don't have a facture yet
        $locations = Location::doesntHave('facture')->with('client', 'voiture')->get();
        return view('factures.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id|unique:factures,location_id',
            'date_facture' => 'required|date',
            'statut' => 'required|in:payée,en attente,annulée',
        ]);

        try {
            $location = Location::find($validated['location_id']);

            // Generate unique invoice number (e.g., FACT-2026-001)
            $count = Facture::whereYear('date_facture', date('Y', strtotime($validated['date_facture'])))->count() + 1;
            $numero_facture = 'FACT-' . date('Y', strtotime($validated['date_facture'])) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $facture = Facture::create([
                'location_id' => $validated['location_id'],
                'numero_facture' => $numero_facture,
                'date_facture' => $validated['date_facture'],
                'montant_total' => $location->tarif_total,
                'statut' => $validated['statut'],
            ]);

            return redirect()->route('factures.show', $facture)->with('success', 'Facture générée avec succès.');
        }
        catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la génération : ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Facture $facture)
    {
        $facture->load('location.client', 'location.voiture', 'location.paiements');
        return view('factures.show', compact('facture'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facture $facture)
    {
        $locations = Location::all();
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
}
