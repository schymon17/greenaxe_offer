<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'company',
        'notes',
    ];

    public function gardenProjects(): HasMany
    {
        return $this->hasMany(GardenProject::class);
    }
}
