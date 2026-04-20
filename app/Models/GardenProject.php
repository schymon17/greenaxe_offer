<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class GardenProject extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'street',
        'city',
        'postal_code',
        'area_m2',
        'status',
        'description',
    ];

    protected $casts = [
        'area_m2' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(GardenSection::class)->orderBy('order');
    }
}
