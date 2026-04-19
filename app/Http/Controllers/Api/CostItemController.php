<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostItem;
use Illuminate\Http\Request;

class CostItemController extends Controller
{
    public function index()
    {
        return CostItem::query()
            ->with('offer')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(50);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:offers,id'],
            'category' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:16'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $quantity = (float) ($data['quantity'] ?? 1);
        $unitPrice = (float) ($data['unit_price'] ?? 0);
        $data['line_total'] = round($quantity * $unitPrice, 2);

        $costItem = CostItem::create($data);
        $costItem->offer->recalculateTotals();

        return response()->json($costItem->fresh(), 201);
    }

    public function show(CostItem $costItem)
    {
        return $costItem->load('offer');
    }

    public function update(Request $request, CostItem $costItem)
    {
        $data = $request->validate([
            'offer_id' => ['sometimes', 'required', 'integer', 'exists:offers,id'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:16'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $offerBefore = $costItem->offer;

        $quantity = (float) ($data['quantity'] ?? $costItem->quantity);
        $unitPrice = (float) ($data['unit_price'] ?? $costItem->unit_price);
        $data['line_total'] = round($quantity * $unitPrice, 2);

        $costItem->update($data);

        if ((int) $offerBefore->id !== (int) $costItem->offer_id) {
            $offerBefore->recalculateTotals();
        }

        $costItem->offer->recalculateTotals();

        return $costItem->fresh();
    }

    public function destroy(CostItem $costItem)
    {
        $offer = $costItem->offer;
        $costItem->delete();
        $offer->recalculateTotals();

        return response()->noContent();
    }
}
