<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        '_sport_id',
        '_location_id',
        'start_time',
        'description'
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, '_sport_id');
    }

    
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, '_location_id');
    }

   
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'event_team', '_event_id', '_team_id')
                    ->withTimestamps();
    }
}
