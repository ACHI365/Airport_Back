<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stop extends Model
{
    use HasFactory;

    protected $fillable = [
        'stop_name',
        'latitude',
        'longitude',
    ];

    public function routesAsStart(): HasMany
    {
        return $this->hasMany(Route::class, 'start_stop_id');
    }

    public function routesAsEnd(): HasMany
    {
        return $this->hasMany(Route::class, 'end_stop_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
