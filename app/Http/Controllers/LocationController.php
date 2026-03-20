<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Client;
use App\Models\Voiture;
use App\Models\Chauffeur;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::with(['client', 'voiture', 'chauffeur'])->get();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $voitures = Voiture::where('statut', 'disponible')->get();
        $chauffeurs = Chauffeur::where('disponible', true)->get();
        return view('locations.create', compact('clients', 'voitures', 'chauffeurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $validated = $request->validated();

        $voiture = Voiture::find($validated['voiture_id']);

        // Prevent renting a car that is not available
        if ($validated['statut'] === 'en cours' && $voiture->statut !== 'disponible') {
            return back()->withInput()->withErrors(['voiture_id' => 'Le véhicule sélectionné n\'est pas disponible (Statut : ' . $voiture->statut . ')']);
        }

        // Handle chauffeur availability if required
        if ($validated['avec_chauffeur'] && $validated['chauffeur_id']) {
            $chauffeur = Chauffeur::find($validated['chauffeur_id']);
            if (!$chauffeur->disponible && $validated['statut'] === 'en cours') {
                return back()->withInput()->withErrors(['chauffeur_id' => 'Le chauffeur sélectionné n\'est pas disponible.']);
            }
        }

        $penalite = 0;
        if ($validated['statut'] === 'terminée' && !empty($validated['date_retour'])) {
            $dateFin = Carbon::parse($validated['date_fin']);
            $dateRetour = Carbon::parse($validated['date_retour']);
            if ($dateRetour->gt($dateFin)) {
                $penalite = $dateRetour->diffInDays($dateFin) * 10;
            }
        }
        $validated['penalite'] = $penalite;

        if (!$validated['avec_chauffeur']) {
            $validated['chauffeur_id'] = null;
        }

        DB::transaction(function () use ($validated, $voiture) {
            $location = Location::create($validated);

            // Update car status if rental is "en cours"
            if ($location->statut === 'en cours') {
                $voiture->update(['statut' => 'louée']);
                if ($location->avec_chauffeur && $location->chauffeur_id) {
                    Chauffeur::where('id', $location->chauffeur_id)->update(['disponible' => false]);
                }
            }
        });

        return redirect()->route('locations.index')->with('success', 'Location créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['client', 'voiture', 'chauffeur']);
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
        // Include the current chauffeur even if "indisponible"
        $chauffeurs = Chauffeur::where('disponible', true)->orWhere('id', $location->chauffeur_id)->get();
        return view('locations.edit', compact('location', 'clients', 'voitures', 'chauffeurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        $validated = $request->validated();

        $penalite = 0;
        if ($validated['statut'] === 'terminée' && !empty($validated['date_retour'])) {
            $dateFin = Carbon::parse($validated['date_fin']);
            $dateRetour = Carbon::parse($validated['date_retour']);
            if ($dateRetour->gt($dateFin)) {
                $penalite = $dateRetour->diffInDays($dateFin) * 10;
            }
        }
        $validated['penalite'] = $penalite;

        if (!$validated['avec_chauffeur']) {
            $validated['chauffeur_id'] = null;
        }

        $oldVoiture = $location->voiture;
        $newVoiture = Voiture::find($validated['voiture_id']);
        $oldChauffeur = $location->chauffeur;
        $newChauffeurId = $validated['chauffeur_id'];

        // Manage Car Status change checks
        if ($oldVoiture->id != $validated['voiture_id']) {
            if ($validated['statut'] === 'en cours' && $newVoiture->statut !== 'disponible') {
                return back()->withInput()->withErrors(['voiture_id' => 'Le véhicule sélectionné n\'est pas disponible.']);
            }
        }

        // Manage Chauffeur Status change checks
        if ($location->chauffeur_id != $newChauffeurId) {
            if ($newChauffeurId && $validated['statut'] === 'en cours') {
                $newChauffeur = Chauffeur::find($newChauffeurId);
                if (!$newChauffeur->disponible) {
                    return back()->withInput()->withErrors(['chauffeur_id' => 'Le chauffeur sélectionné n\'est pas disponible.']);
                }
            }
        }

        DB::transaction(function () use ($validated, $location, $oldVoiture, $newVoiture, $oldChauffeur, $newChauffeurId) {
            if ($oldVoiture->id != $validated['voiture_id']) {
                $oldVoiture->update(['statut' => 'disponible']);
            }

            if ($location->chauffeur_id != $newChauffeurId) {
                if ($oldChauffeur) {
                    $oldChauffeur->update(['disponible' => true]);
                }
            }

            $location->update($validated);

            // Final synchronization
            if ($location->statut === 'en cours') {
                $newVoiture->update(['statut' => 'louée']);
                if ($location->avec_chauffeur && $location->chauffeur_id) {
                    Chauffeur::where('id', $location->chauffeur_id)->update(['disponible' => false]);
                }
            } else {
                $newVoiture->update(['statut' => 'disponible']);
                if ($location->chauffeur_id) {
                    Chauffeur::where('id', $location->chauffeur_id)->update(['disponible' => true]);
                }
            }
        });

        return redirect()->route('locations.index')->with('success', 'Location mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $voiture = $location->voiture;
        $chauffeur = $location->chauffeur;
        $location->delete();

        // Make car and chauffeur available again if the rental was active
        if ($location->statut === 'en cours') {
            $voiture->update(['statut' => 'disponible']);
            if ($chauffeur) {
                $chauffeur->update(['disponible' => true]);
            }
        }

        return redirect()->route('locations.index')->with('success', 'Location supprimée avec succès.');
    }
}
