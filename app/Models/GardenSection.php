<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GardenSection extends Model
{
    protected $fillable = [
        'garden_project_id',
        'name',
        'description',
        'canvas_data',
        'order',
    ];

    protected $casts = [
        'canvas_data' => 'array',
    ];

    public function gardenProject(): BelongsTo
    {
        return $this->belongsTo(GardenProject::class);
    }

    public function elements(): HasMany
    {
        return $this->hasMany(SectionElement::class)->orderBy('order');
    }

    public function getTotalCostAttribute(): float
    {
        return $this->elements->sum(fn($el) => $el->quantity * $el->unit_price);
    }
}
