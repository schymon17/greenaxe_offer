<?php

namespace App\Http\Controllers;

use App\Models\GardenProject;
use App\Models\Client;
use Illuminate\Http\Request;

class GardenProjectController extends Controller
{
    public function index()
    {
        $projects = GardenProject::with('client')->paginate(10);
        return view('garden-projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::all();
        $gardenProject = null;
        return view('garden-projects.form', compact('gardenProject', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'area_m2' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        GardenProject::create($validated);

        return redirect()->route('garden-projects.index')->with('success', 'Projekt został utworzony');
    }

    public function show(GardenProject $gardenProject)
    {
        $gardenProject->load(['sections.elements', 'client']);
        return view('garden-projects.show', compact('gardenProject'));
    }

    public function edit(GardenProject $gardenProject)
    {
        $clients = Client::all();
        return view('garden-projects.form', compact('gardenProject', 'clients'));
    }

    public function update(Request $request, GardenProject $gardenProject)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'area_m2' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $gardenProject->update($validated);

        return redirect()->route('garden-projects.index')->with('success', 'Projekt został zaktualizowany');
    }

    public function destroy(GardenProject $gardenProject)
    {
        $gardenProject->delete();

        return redirect()->route('garden-projects.index')->with('success', 'Projekt został usunięty');
    }
}
