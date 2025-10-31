<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', '_sport_id'];

    
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class, '_sport_id');
    }
 
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_team', '_team_id', '_event_id')
                    ->withTimestamps();
    }
}
