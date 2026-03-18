<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::all();
        return view("clients.index", compact("clients"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("clients.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'personne');

        $rules = [
            'type' => 'required|in:personne,association',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
        ];

        if ($type === 'personne') {
            $rules['nom'] = 'required|string|max:255';
            $rules['prenom'] = 'nullable|string|max:255';
            $rules['date_naissance'] = 'nullable|date';
            $rules['cin'] = 'nullable|string|max:20';
        }
        else {
            $rules['raison_sociale'] = 'required|string|max:255';
            $rules['nif'] = 'nullable|string|max:20';
            $rules['stat'] = 'nullable|string|max:20';
        }

        $validated = $request->validate($rules);

        Client::create($validated);

        return redirect()->route("clients.index")
            ->with("success", "Client créé avec succès.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view("clients.show", compact("client"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view("clients.edit", compact("client"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $type = $request->input('type', $client->type);

        $rules = [
            'type' => 'required|in:personne,association',
            'telephone' => 'required|string|max:20',
            'adresse' => 'required|string|max:255',
        ];

        if ($type === 'personne') {
            $rules['nom'] = 'required|string|max:255';
            $rules['prenom'] = 'nullable|string|max:255';
            $rules['date_naissance'] = 'nullable|date';
            $rules['cin'] = 'nullable|string|max:20';
        }
        else {
            $rules['raison_sociale'] = 'required|string|max:255';
            $rules['nif'] = 'nullable|string|max:20';
            $rules['stat'] = 'nullable|string|max:20';
        }

        $validated = $request->validate($rules);

        $client->update($validated);

        return redirect()->route("clients.index")
            ->with("success", "Client mis à jour avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route("clients.index")
            ->with("success", "Client supprimé avec succès.");
    }
}
