<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GardenProject;
use Illuminate\Http\Request;

class GardenProjectController extends Controller
{
    public function index()
    {
        return GardenProject::query()
            ->with('client')
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
        ]);

        return response()->json(GardenProject::create($data), 201);
    }

    public function show(GardenProject $gardenProject)
    {
        return $gardenProject->load('client', 'offers');
    }

    public function update(Request $request, GardenProject $gardenProject)
    {
        $data = $request->validate([
            'client_id' => ['sometimes', 'required', 'integer', 'exists:clients,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
        ]);

        $gardenProject->update($data);

        return $gardenProject->fresh();
    }

    public function destroy(GardenProject $gardenProject)
    {
        $gardenProject->delete();

        return response()->noContent();
    }
}
