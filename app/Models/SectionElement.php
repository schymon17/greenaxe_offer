<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionElement extends Model
{
    protected $fillable = [
        'garden_section_id',
        'zone_ref',
        'zone_label',
        'type',
        'name',
        'material',
        'quantity',
        'unit',
        'unit_price',
        'notes',
        'order',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(GardenSection::class, 'garden_section_id');
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->quantity * (float) $this->unit_price;
    }
}
