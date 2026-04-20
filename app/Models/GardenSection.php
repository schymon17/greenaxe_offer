<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GardenSection extends Model
{
    protected $fillable = [
        'garden_project_id',
        'name',
        'description',
        'canvas_data',
        'order',
        'public_token',
    ];

    protected $casts = [
        'canvas_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->public_token) {
                do {
                    $token = Str::random(32);
                } while (static::where('public_token', $token)->exists());
                $model->public_token = $token;
            }
        });
    }

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
