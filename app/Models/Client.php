<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'notes',
        'address',
        'city',
        'postal_code',
        'contact_person',
        'contact_position',
        'contact_phone',
        'last_contact_date',
        'preferred_contact_method',
        'status',
        'source',
        'contact_history',
    ];

    public function gardenProjects(): HasMany
    {
        return $this->hasMany(GardenProject::class);
    }
}
