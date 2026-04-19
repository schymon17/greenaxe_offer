<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'garden_project_id',
        'number',
        'title',
        'status',
        'currency',
        'valid_until',
        'labor_cost',
        'material_cost',
        'margin_percent',
        'total_net',
        'tax_percent',
        'total_gross',
        'notes',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'labor_cost' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'margin_percent' => 'decimal:2',
        'total_net' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total_gross' => 'decimal:2',
    ];

    public function gardenProject(): BelongsTo
    {
        return $this->belongsTo(GardenProject::class);
    }

    public function costItems(): HasMany
    {
        return $this->hasMany(CostItem::class);
    }

    public function recalculateTotals(): void
    {
        $materialCost = (float) $this->costItems()->sum('line_total');
        $laborCost = (float) $this->labor_cost;
        $marginFactor = 1 + ((float) $this->margin_percent / 100);
        $taxFactor = 1 + ((float) $this->tax_percent / 100);

        $totalNet = ($laborCost + $materialCost) * $marginFactor;
        $totalGross = $totalNet * $taxFactor;

        $this->forceFill([
            'material_cost' => round($materialCost, 2),
            'total_net' => round($totalNet, 2),
            'total_gross' => round($totalGross, 2),
        ])->save();
    }
}
