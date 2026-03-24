<?php

namespace App\Http\Controllers;

use App\Models\ComptePaiement;
use Illuminate\Http\Request;

class ComptePaiementController extends Controller
{
    public function index()
    {
        $comptes = ComptePaiement::all();
        return view('comptes.index', compact('comptes'));
    }

    public function create()
    {
        return view('comptes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bancaire,mobile_money',
            'nom' => 'required|string|max:255',
            'details' => 'required|string|max:255',
            'actif' => 'nullable'
        ]);

        $validated['actif'] = $request->has('actif');

        ComptePaiement::create($validated);

        return redirect()->route('comptes.index')->with('success', 'Compte de paiement ajouté avec succès.');
    }

    public function edit(ComptePaiement $compte)
    {
        return view('comptes.edit', compact('compte'));
    }

    public function update(Request $request, ComptePaiement $compte)
    {
        $validated = $request->validate([
            'type' => 'required|in:bancaire,mobile_money',
            'nom' => 'required|string|max:255',
            'details' => 'required|string|max:255',
            'actif' => 'nullable'
        ]);

        $validated['actif'] = $request->has('actif');

        $compte->update($validated);

        return redirect()->route('comptes.index')->with('success', 'Compte de paiement mis à jour avec succès.');
    }

    public function destroy(ComptePaiement $compte)
    {
        $compte->delete();
        return redirect()->route('comptes.index')->with('success', 'Compte de paiement supprimé avec succès.');
    }
}
