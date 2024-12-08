<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_stop_id',
        'end_stop_id',
        'distance_km',
        'price'
    ];

    public function startStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'start_stop_id');
    }

    public function endStop(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'end_stop_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
