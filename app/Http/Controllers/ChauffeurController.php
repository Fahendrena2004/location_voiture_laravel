<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use Illuminate\Http\Request;

class ChauffeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chauffeurs = Chauffeur::all();
        return view('chauffeurs.index', compact('chauffeurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('chauffeurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'categorie_permis' => 'required|string|max:50',
            'disponible' => 'boolean',
        ]);

        Chauffeur::create($validated);

        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Chauffeur $chauffeur)
    {
        return view('chauffeurs.show', compact('chauffeur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chauffeur $chauffeur)
    {
        return view('chauffeurs.edit', compact('chauffeur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chauffeur $chauffeur)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'categorie_permis' => 'required|string|max:50',
            'disponible' => 'boolean',
        ]);

        $chauffeur->update($validated);

        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chauffeur $chauffeur)
    {
        $chauffeur->delete();
        return redirect()->route('chauffeurs.index')->with('success', 'Chauffeur supprimé avec succès.');
    }
}
