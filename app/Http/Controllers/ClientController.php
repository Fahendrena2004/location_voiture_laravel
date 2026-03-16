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
        //
        $clients = Client::all();
        return view("clients.index", compact("clients"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view("clients.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            "nom" => "required|string|max:255",
            "prenom" => "required|string|max:255",
            "date_naissance" => "required|date",
            "telephone" => "required|string|max:20",
            "adresse" => "required|string|max:255",
        ]);

        Client::create($validated);
        return redirect()->route("clients.index")
            ->with("success", "Client créé avec succès.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
        return view("clients.show", compact("client"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
        return view("clients.edit", compact("client"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
        $validated = $request->validate([
            "nom" => "required|string|max:255",
            "prenom" => "required|string|max:255",
            "date_naissance" => "required|date",
            "telephone" => "required|string|max:20",
            "adresse" => "required|string|max:255",
        ]);

        $client->update($validated);
        return redirect()->route("clients.index")
            ->with("success", "Client mis à jour avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
        $client->delete();
        return redirect()->route("clients.index")
            ->with("success", "Client supprimé avec succès.");


    }
}
