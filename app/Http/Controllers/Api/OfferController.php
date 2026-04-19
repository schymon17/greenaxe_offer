<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        return Offer::query()
            ->with('gardenProject')
            ->latest()
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'garden_project_id' => ['required', 'integer', 'exists:garden_projects,id'],
            'number' => ['required', 'string', 'max:255', 'unique:offers,number'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:64'],
            'currency' => ['nullable', 'string', 'size:3'],
            'valid_until' => ['nullable', 'date'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'margin_percent' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $offer = Offer::create($data);
        $offer->recalculateTotals();

        return response()->json($offer->fresh(), 201);
    }

    public function show(Offer $offer)
    {
        return $offer->load('gardenProject', 'costItems');
    }

    public function update(Request $request, Offer $offer)
    {
        $data = $request->validate([
            'garden_project_id' => ['sometimes', 'required', 'integer', 'exists:garden_projects,id'],
            'number' => ['sometimes', 'required', 'string', 'max:255', 'unique:offers,number,'.$offer->id],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:64'],
            'currency' => ['nullable', 'string', 'size:3'],
            'valid_until' => ['nullable', 'date'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'margin_percent' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $offer->update($data);
        $offer->recalculateTotals();

        return $offer->fresh();
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->noContent();
    }
}
