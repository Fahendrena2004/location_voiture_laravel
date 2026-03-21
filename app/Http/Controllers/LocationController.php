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
    public function index()
    {
        $query = Location::with(['client', 'voitures', 'chauffeurs']);

        if (auth()->user()->isClient()) {
            $query->whereHas('client', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $locations = $query->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        if (!auth()->user()->isClient()) {
            abort(403, 'Seuls les clients peuvent effectuer une réservation.');
        }

        $client = auth()->user()->client;
        if (!$client) {
            return redirect()->route('dashboard')->with('error', 'Vous devez avoir un profil client lié pour réserver.');
        }
        $clients = collect([$client]);

        $voitures = Voiture::where('statut', 'disponible')->get();
        $chauffeurs = Chauffeur::where('disponible', true)->get();
        return view('locations.create', compact('clients', 'voitures', 'chauffeurs'));
    }

    public function store(StoreLocationRequest $request)
    {
        if (!auth()->user()->isClient()) {
            abort(403, 'Seuls les clients peuvent effectuer une réservation.');
        }

        $validated = $request->validated();

        $voitures = Voiture::whereIn('id', $validated['voitures'])->get();
        $chauffeursIds = $validated['chauffeurs'] ?? [];

        if ($validated['statut'] === 'en cours') {
            foreach ($voitures as $voiture) {
                if ($voiture->statut !== 'disponible') {
                    return back()->withInput()->withErrors(['voitures' => 'Le véhicule ' . $voiture->marque . ' n\'est pas disponible.']);
                }
            }

            if (!empty($chauffeursIds)) {
                $chauffeurs = Chauffeur::whereIn('id', $chauffeursIds)->get();
                foreach ($chauffeurs as $chauffeur) {
                    if (!$chauffeur->disponible) {
                        return back()->withInput()->withErrors(['chauffeurs' => 'Le chauffeur ' . $chauffeur->nom . ' n\'est pas disponible.']);
                    }
                }
            }
        } elseif ($validated['statut'] === 'en attente' && auth()->user()->isAdmin()) {
            // Admin can also set things to "en attente" but maybe we don't need special check here
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
        $validated['avec_chauffeur'] = $request->has('avec_chauffeur');

        $voituresIds = $validated['voitures'];
        unset($validated['voitures']);
        unset($validated['chauffeurs']);

        DB::transaction(function () use ($validated, $voitures, $voituresIds, $chauffeursIds) {
            $location = Location::create($validated);
            $location->voitures()->attach($voituresIds);
            $location->chauffeurs()->attach($chauffeursIds);

            if ($location->statut === 'en cours') {
                Voiture::whereIn('id', $voituresIds)->update(['statut' => 'louée']);
                if (!empty($chauffeursIds)) {
                    Chauffeur::whereIn('id', $chauffeursIds)->update(['disponible' => false]);
                }
            }
        });

        return redirect()->route('locations.index')->with('success', 'Location créée avec succès.');
    }

    public function show(Location $location)
    {
        if (auth()->user()->isClient() && $location->client->user_id !== auth()->id()) {
            abort(403);
        }
        $location->load(['client', 'voitures', 'chauffeurs']);
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        if (auth()->user()->isClient()) {
            if ($location->client->user_id !== auth()->id() || $location->statut !== 'en attente') {
                abort(403);
            }
            $clients = collect([$location->client]);
        } else {
            $clients = Client::all();
        }
        $currentVoituresIds = $location->voitures->pluck('id')->toArray();
        $currentChauffeursIds = $location->chauffeurs->pluck('id')->toArray();
        $voitures = Voiture::where('statut', 'disponible')->orWhereIn('id', $currentVoituresIds)->get();
        $chauffeurs = Chauffeur::where('disponible', true)->orWhereIn('id', $currentChauffeursIds)->get();
        return view('locations.edit', compact('location', 'clients', 'voitures', 'chauffeurs'));
    }

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

        $newVoituresIds = $validated['voitures'];
        $newVoitures = Voiture::whereIn('id', $newVoituresIds)->get();
        $oldVoituresIds = $location->voitures->pluck('id')->toArray();

        $newChauffeursIds = $validated['chauffeurs'] ?? [];
        $oldChauffeursIds = $location->chauffeurs->pluck('id')->toArray();

        if ($validated['statut'] === 'en cours') {
            foreach ($newVoitures as $newVoiture) {
                if (!in_array($newVoiture->id, $oldVoituresIds) && $newVoiture->statut !== 'disponible') {
                    return back()->withInput()->withErrors(['voitures' => 'Le véhicule ' . $newVoiture->marque . ' n\'est pas disponible.']);
                }
            }

            if (!empty($newChauffeursIds)) {
                $newChauffeurs = Chauffeur::whereIn('id', $newChauffeursIds)->get();
                foreach ($newChauffeurs as $newChauffeur) {
                    if (!in_array($newChauffeur->id, $oldChauffeursIds) && !$newChauffeur->disponible) {
                        return back()->withInput()->withErrors(['chauffeurs' => 'Le chauffeur ' . $newChauffeur->nom . ' n\'est pas disponible.']);
                    }
                }
            }
        }

        unset($validated['voitures']);
        unset($validated['chauffeurs']);

        DB::transaction(function () use ($validated, $location, $oldVoituresIds, $newVoituresIds, $oldChauffeursIds, $newChauffeursIds) {
            $voituresToRelease = array_diff($oldVoituresIds, $newVoituresIds);
            if (!empty($voituresToRelease)) {
                Voiture::whereIn('id', $voituresToRelease)->update(['statut' => 'disponible']);
            }

            $chauffeursToRelease = array_diff($oldChauffeursIds, $newChauffeursIds);
            if (!empty($chauffeursToRelease)) {
                Chauffeur::whereIn('id', $chauffeursToRelease)->update(['disponible' => true]);
            }

            $location->update($validated);
            $location->voitures()->sync($newVoituresIds);
            $location->chauffeurs()->sync($newChauffeursIds);

            if ($location->statut === 'en cours') {
                Voiture::whereIn('id', $newVoituresIds)->update(['statut' => 'louée']);
                if (!empty($newChauffeursIds)) {
                    Chauffeur::whereIn('id', $newChauffeursIds)->update(['disponible' => false]);
                }
            } else {
                Voiture::whereIn('id', $newVoituresIds)->update(['statut' => 'disponible']);
                if (!empty($newChauffeursIds)) {
                    Chauffeur::whereIn('id', $newChauffeursIds)->update(['disponible' => true]);
                }
            }
        });

        return redirect()->route('locations.index')->with('success', 'Location mise à jour avec succès.');
    }

    public function destroy(Location $location)
    {
        $voituresIds = $location->voitures->pluck('id')->toArray();
        $chauffeursIds = $location->chauffeurs->pluck('id')->toArray();
        $statut = $location->statut;

        $location->delete();

        if ($statut === 'en cours') {
            if (!empty($voituresIds)) {
                Voiture::whereIn('id', $voituresIds)->update(['statut' => 'disponible']);
            }
            if (!empty($chauffeursIds)) {
                Chauffeur::whereIn('id', $chauffeursIds)->update(['disponible' => true]);
            }
        }

        return redirect()->route('locations.index')->with('success', 'Location supprimée avec succès.');
    }
}
