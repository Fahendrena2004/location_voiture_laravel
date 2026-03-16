<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Client;
use App\Models\Voiture;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::with(['client', 'voiture'])->get();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $voitures = Voiture::where('statut', 'disponible')->get();
        return view('locations.create', compact('clients', 'voitures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'voiture_id' => 'required|exists:voitures,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'tarif_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en cours,terminée,annulée',
        ]);

        $voiture = Voiture::find($validated['voiture_id']);

        // Prevent renting a car that is not available
        if ($validated['statut'] === 'en cours' && $voiture->statut !== 'disponible') {
            return back()->withInput()->withErrors(['voiture_id' => 'Le véhicule sélectionné n\'est pas disponible (Statut : ' . $voiture->statut . ')']);
        }

        $location = Location::create($validated);

        // Update car status if rental is "en cours"
        if ($location->statut === 'en cours') {
            $location->voiture->update(['statut' => 'louée']);
        }

        return redirect()->route('locations.index')->with('success', 'Location créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['client', 'voiture']);
        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        $clients = Client::all();
        // Include the current car even if it's not "disponible" anymore
        $voitures = Voiture::where('statut', 'disponible')->orWhere('id', $location->voiture_id)->get();
        return view('locations.edit', compact('location', 'clients', 'voitures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'voiture_id' => 'required|exists:voitures,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'tarif_total' => 'required|numeric|min:0',
            'statut' => 'required|in:en cours,terminée,annulée',
        ]);

        $oldVoiture = $location->voiture;
        $oldStatut = $location->statut;
        $newVoiture = Voiture::find($validated['voiture_id']);

        // Logic for car or status change
        if ($oldVoiture->id != $validated['voiture_id']) {
            // Revert old car status
            if ($oldStatut === 'en cours') {
                $oldVoiture->update(['statut' => 'disponible']);
            }

            // Check new car availability
            if ($validated['statut'] === 'en cours' && $newVoiture->statut !== 'disponible') {
                return back()->withInput()->withErrors(['voiture_id' => 'Le nouveau véhicule sélectionné n\'est pas disponible (Statut : ' . $newVoiture->statut . ')']);
            }
        }

        $location->update($validated);

        // Final status synchronization
        if ($location->statut === 'en cours') {
            $location->voiture->update(['statut' => 'louée']);
        }
        else {
            // If the status is "terminée" or "annulée", make the car available again
            $location->voiture->update(['statut' => 'disponible']);
        }

        return redirect()->route('locations.index')->with('success', 'Location mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $voiture = $location->voiture;
        $location->delete();

        // Make car available again if the rental was active
        if ($location->statut === 'en cours') {
            $voiture->update(['statut' => 'disponible']);
        }

        return redirect()->route('locations.index')->with('success', 'Location supprimée avec succès.');
    }
}
