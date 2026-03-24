<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use Illuminate\Http\Request;

class VoitureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voitures = Voiture::all();
        return view('voitures.index', compact('voitures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('voitures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'immatriculation' => 'required|string|max:255|unique:voitures',
            'couleur' => 'nullable|string|max:255',
            'nombre_places' => 'required|integer|min:1',
            'prix_journalier' => 'required|numeric|min:0',
            'statut' => 'required|in:disponible,louée,en entretien',
            'categorie' => 'required|string|in:Berline,SUV,Citadine,Luxe,Utilitaire',
        ]);

        Voiture::create($validated);

        return redirect()->route('voitures.index')->with('success', 'Voiture créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Voiture $voiture)
    {
        return view('voitures.show', compact('voiture'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Voiture $voiture)
    {
        return view('voitures.edit', compact('voiture'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Voiture $voiture)
    {
        $validated = $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'immatriculation' => 'required|string|max:255|unique:voitures,immatriculation,' . $voiture->id,
            'couleur' => 'nullable|string|max:255',
            'nombre_places' => 'required|integer|min:1',
            'prix_journalier' => 'required|numeric|min:0',
            'statut' => 'required|in:disponible,louée,en entretien',
            'categorie' => 'required|string|in:Berline,SUV,Citadine,Luxe,Utilitaire',
        ]);

        $voiture->update($validated);

        return redirect()->route('voitures.index')->with('success', 'Voiture mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Voiture $voiture)
    {
        $voiture->delete();

        return redirect()->route('voitures.index')->with('success', 'Voiture supprimée avec succès.');
    }
}
