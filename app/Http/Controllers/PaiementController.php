<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Location;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Paiement::with('location.client');

        if (auth()->user()->isClient()) {
            $query->whereHas('location.client', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $paiements = $query->get();
        return view('paiements.index', compact('paiements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $query = Location::with('client', 'voitures');

        if (auth()->user()->isClient()) {
            $query->whereHas('client', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        $locations = $query->get();
        return view('paiements.create', compact('locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => [
                'required',
                'exists:locations,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->isClient()) {
                        $location = Location::find($value);
                        if (!$location || $location->client->user_id !== auth()->id()) {
                            $fail('Vous n\'êtes pas autorisé à payer pour cette location.');
                        }
                    }
                },
            ],
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:espèces,bancaire,mobile_money',
            'numero_mobile' => 'nullable|required_if:mode_paiement,mobile_money|string|max:50',
            'numero_bordereau' => 'nullable|required_if:mode_paiement,bancaire|string|max:100',
            'nom_banque' => 'nullable|required_if:mode_paiement,bancaire|string|max:100',
        ]);

        Paiement::create($validated);

        return redirect()->route('paiements.index')->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        if (auth()->user()->isClient() && $paiement->location->client->user_id !== auth()->id()) {
            abort(403);
        }
        $paiement->load('location.client', 'location.voitures');
        return view('paiements.show', compact('paiement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiement $paiement)
    {
        if (auth()->user()->isClient() && $paiement->location->client->user_id !== auth()->id()) {
            abort(403);
        }

        $query = Location::with('client', 'voitures');
        if (auth()->user()->isClient()) {
            $query->whereHas('client', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }
        $locations = $query->get();

        return view('paiements.edit', compact('paiement', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'location_id' => [
                'required',
                'exists:locations,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->isClient()) {
                        $location = Location::find($value);
                        if (!$location || $location->client->user_id !== auth()->id()) {
                            $fail('Vous n\'êtes pas autorisé à payer pour cette location.');
                        }
                    }
                },
            ],
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:espèces,bancaire,mobile_money',
            'numero_mobile' => 'nullable|required_if:mode_paiement,mobile_money|string|max:50',
            'numero_bordereau' => 'nullable|required_if:mode_paiement,bancaire|string|max:100',
            'nom_banque' => 'nullable|required_if:mode_paiement,bancaire|string|max:100',
        ]);

        $paiement->update($validated);

        return redirect()->route('paiements.index')->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        if (auth()->user()->isClient()) {
            abort(403);
        }

        $paiement->delete();

        return redirect()->route('paiements.index')->with('success', 'Paiement supprimé avec succès.');
    }
}
