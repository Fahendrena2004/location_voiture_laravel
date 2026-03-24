<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Voiture;
use App\Models\Location;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $clients = Client::where('nom', 'like', "%{$query}%")
            ->orWhere('prenom', 'like', "%{$query}%")
            ->orWhere('telephone', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($client) {
                return [
                    'type' => 'Client',
                    'title' => "{$client->nom} {$client->prenom}",
                    'subtitle' => $client->telephone,
                    'url' => route('clients.show', $client),
                    'icon' => 'users',
                ];
            });

        $voitures = Voiture::where('marque', 'like', "%{$query}%")
            ->orWhere('modele', 'like', "%{$query}%")
            ->orWhere('immatriculation', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($voiture) {
                return [
                    'type' => 'Voiture',
                    'title' => "{$voiture->marque} {$voiture->modele}",
                    'subtitle' => $voiture->immatriculation,
                    'url' => route('voitures.show', $voiture),
                    'icon' => 'truck',
                ];
            });

        $results = $clients->merge($voitures);

        // Location search (only for admins or current client)
        $locationsQuery = Location::query();
        if (!auth()->user()->isAdmin()) {
            $locationsQuery->where('client_id', auth()->user()->client->id);
        }

        $locations = $locationsQuery->whereHas('client', function ($q) use ($query) {
            $q->where('nom', 'like', "%{$query}%")
                ->orWhere('prenom', 'like', "%{$query}%");
        })
            ->orWhereHas('voitures', function ($q) use ($query) {
                $q->where('marque', 'like', "%{$query}%")
                    ->orWhere('modele', 'like', "%{$query}%")
                    ->orWhere('immatriculation', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($location) {
                return [
                    'type' => 'Location',
                    'title' => "Location #{$location->id}",
                    'subtitle' => $location->client->nom . " - " . $location->voitures->first()?->marque . " " . $location->voitures->first()?->modele,
                    'url' => route('locations.show', $location),
                    'icon' => 'calendar',
                ];
            });

        $results = $results->merge($locations);

        return response()->json($results);
    }
}
