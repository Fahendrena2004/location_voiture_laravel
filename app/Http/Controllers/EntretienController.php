<?php

namespace App\Http\Controllers;

use App\Models\Entretien;
use App\Models\Voiture;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entretiens = Entretien::with('voiture')->get();
        return view('entretiens.index', compact('entretiens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $voitures = Voiture::where('statut', 'disponible')->get();
        return view('entretiens.create', compact('voitures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voiture_id' => 'required|exists:voitures,id',
            'date_entretien' => 'required|date',
            'description' => 'required|string',
            'cout' => 'required|numeric|min:0',
        ]);

        $entretien = Entretien::create($validated);

        // Update car status to "en entretien"
        $entretien->voiture->update(['statut' => 'en entretien']);

        return redirect()->route('entretiens.index')->with('success', 'Entretien enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Entretien $entretien)
    {
        $entretien->load('voiture');
        return view('entretiens.show', compact('entretien'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entretien $entretien)
    {
        // Include current car
        $voitures = Voiture::where('statut', 'disponible')->orWhere('id', $entretien->voiture_id)->get();
        return view('entretiens.edit', compact('entretien', 'voitures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entretien $entretien)
    {
        $validated = $request->validate([
            'voiture_id' => 'required|exists:voitures,id',
            'date_entretien' => 'required|date',
            'description' => 'required|string',
            'cout' => 'required|numeric|min:0',
        ]);

        $entretien->update($validated);

        return redirect()->route('entretiens.index')->with('success', 'Entretien mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entretien $entretien)
    {
        $voiture = $entretien->voiture;
        $entretien->delete();

        // Check if the car has other active maintenance records (optional, but good practice)
        // For simplicity, we just set it back to disponible
        $voiture->update(['statut' => 'disponible']);

        return redirect()->route('entretiens.index')->with('success', 'Entretien supprimé avec succès.');
    }
}
